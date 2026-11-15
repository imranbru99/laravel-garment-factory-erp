<?php

namespace App\Filament\Resources;

use App\Filament\Support\FieldBuilder;
use App\Filament\Support\GuardsModule;
use App\Models\GoodsReceipt;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class GoodsReceiptResource extends Resource
{
    use GuardsModule;

    protected static ?string $model = GoodsReceipt::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box-arrow-down';

    protected static ?string $navigationGroup = 'Materials';

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationLabel = 'GoodsReceipt';

    public static function module(): string
    {
        return 'procurement';
    }

    public static function roles(): array
    {
        return [
    'store',
    'qc',
];
    }

    public static function allowsChanges(): bool
    {
        return true;
    }

    public static function fields(): array
    {
        return [
            [
                'name' => 'purchase_order_id',
                'type' => 'relation',
                'relation' => 'purchaseOrder',
                'title' => 'po_number',
                'required' => true,
                'filter' => true,
            ],
            [
                'name' => 'factory_id',
                'type' => 'relation',
                'relation' => 'factory',
                'title' => 'name',
                'required' => true,
            ],
            [
                'name' => 'grn_number',
                'type' => 'text',
                'required' => true,
                'search' => true,
                'label' => 'GRN',
            ],
            [
                'name' => 'received_at',
                'type' => 'datetime',
            ],
            [
                'name' => 'qc_status',
                'type' => 'select',
                'options' => 'qc_results',
                'default' => 'pending',
                'filter' => true,
                'badge' => true,
            ],
            [
                'name' => 'notes',
                'type' => 'textarea',
            ],
        ];
    }

    public static function form(Form $form): Form
    {
        return $form->schema(FieldBuilder::form(static::fields()))->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns(FieldBuilder::columns(static::fields()))
            ->filters(FieldBuilder::filters(static::fields()))
            ->defaultSort('id', 'desc')
            ->actions([
                Tables\Actions\Action::make('ocr')
                    ->label('Read document')
                    ->icon('heroicon-o-document-magnifying-glass')
                    ->form([
                        \Filament\Forms\Components\FileUpload::make('document')->required()->disk('local')->directory('ocr-inbox'),
                        \Filament\Forms\Components\Select::make('type')->options(['invoice' => 'Invoice', 'packing_list' => 'Packing list'])->default('invoice')->required(),
                    ])
                    ->action(function ($record, array $data) {
                        $payload = app(\App\Services\Documents\DocumentIntake::class)->extract(storage_path('app/'.$data['document']), $data['type']);
                        app(\App\Services\Documents\DocumentIntake::class)->attachToReceipt($record, $payload);
                        \Filament\Notifications\Notification::make()
                            ->title($payload['ok'] ? 'Document read' : 'Stored with a warning')
                            ->body($payload['warnings'][0] ?? 'Fields are saved on this GRN.')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([Tables\Actions\DeleteBulkAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListGoodsReceipt::route('/'),
            'create' => CreateGoodsReceipt::route('/create'),
            'edit' => EditGoodsReceipt::route('/{record}/edit'),
        ];
    }
}

class ListGoodsReceipt extends ListRecords
{
    protected static string $resource = GoodsReceiptResource::class;

    protected function getHeaderActions(): array
    {
        return [\Filament\Actions\CreateAction::make()];
    }
}

class CreateGoodsReceipt extends CreateRecord
{
    protected static string $resource = GoodsReceiptResource::class;
}

class EditGoodsReceipt extends EditRecord
{
    protected static string $resource = GoodsReceiptResource::class;
}
