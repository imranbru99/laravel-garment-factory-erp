<?php

namespace App\Filament\Resources;

use App\Filament\Support\FieldBuilder;
use App\Filament\Support\GuardsModule;
use App\Models\FinishingBatch;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class FinishingBatchResource extends Resource
{
    use GuardsModule;

    protected static ?string $model = FinishingBatch::class;

    protected static ?string $navigationIcon = 'heroicon-o-fire';

    protected static ?string $navigationGroup = 'Wash & Pack';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'FinishingBatch';

    public static function module(): string
    {
        return 'washing';
    }

    public static function roles(): array
    {
        return [
    'production_manager',
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
                'name' => 'factory_id',
                'type' => 'relation',
                'relation' => 'factory',
                'title' => 'name',
                'required' => true,
            ],
            [
                'name' => 'buyer_order_id',
                'type' => 'relation',
                'relation' => 'buyerOrder',
                'title' => 'po_number',
                'required' => true,
                'filter' => true,
            ],
            [
                'name' => 'batch_no',
                'type' => 'text',
                'required' => true,
                'search' => true,
            ],
            [
                'name' => 'quantity',
                'type' => 'number',
                'required' => true,
            ],
            [
                'name' => 'pressed_qty',
                'type' => 'number',
            ],
            [
                'name' => 'trimmed_qty',
                'type' => 'number',
            ],
            [
                'name' => 'status',
                'type' => 'select',
                'options' => [
                    'queued' => 'Queued',
                    'pressing' => 'Pressing',
                    'trimming' => 'Trimming',
                    'done' => 'Done',
                ],
                'default' => 'queued',
                'filter' => true,
                'badge' => true,
            ],
            [
                'name' => 'finished_at',
                'type' => 'datetime',
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
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()])
            ->bulkActions([Tables\Actions\DeleteBulkAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFinishingBatch::route('/'),
            'create' => CreateFinishingBatch::route('/create'),
            'edit' => EditFinishingBatch::route('/{record}/edit'),
        ];
    }
}

class ListFinishingBatch extends ListRecords
{
    protected static string $resource = FinishingBatchResource::class;

    protected function getHeaderActions(): array
    {
        return [\Filament\Actions\CreateAction::make()];
    }
}

class CreateFinishingBatch extends CreateRecord
{
    protected static string $resource = FinishingBatchResource::class;
}

class EditFinishingBatch extends EditRecord
{
    protected static string $resource = FinishingBatchResource::class;
}
