<?php

namespace App\Filament\Resources;

use App\Filament\Support\FieldBuilder;
use App\Filament\Support\GuardsModule;
use App\Models\CutPlan;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CutPlanResource extends Resource
{
    use GuardsModule;

    protected static ?string $model = CutPlan::class;

    protected static ?string $navigationIcon = 'heroicon-o-scissors';

    protected static ?string $navigationGroup = 'Cutting';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'CutPlan';

    public static function module(): string
    {
        return 'cutting';
    }

    public static function roles(): array
    {
        return [
    'cutting_master',
    'production_manager',
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
                'name' => 'marker_id',
                'type' => 'relation',
                'relation' => 'marker',
                'title' => 'name',
                'required' => true,
                'filter' => true,
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
                'name' => 'plan_date',
                'type' => 'date',
                'required' => true,
            ],
            [
                'name' => 'lay_no',
                'type' => 'text',
                'required' => true,
                'search' => true,
                'label' => 'Lay no',
            ],
            [
                'name' => 'plies',
                'type' => 'number',
            ],
            [
                'name' => 'shade',
                'type' => 'text',
            ],
            [
                'name' => 'lot',
                'type' => 'text',
            ],
            [
                'name' => 'cut_qty',
                'type' => 'number',
            ],
            [
                'name' => 'status',
                'type' => 'select',
                'options' => [
                    'planned' => 'Planned',
                    'spreading' => 'Spreading',
                    'cutting' => 'Cutting',
                    'bundled' => 'Bundled',
                    'closed' => 'Closed',
                ],
                'default' => 'planned',
                'filter' => true,
                'badge' => true,
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
                Tables\Actions\Action::make('ticket')->label('Cut ticket')->icon('heroicon-o-document-arrow-down')->url(fn ($record) => route('pdf.cut-ticket', $record))->openUrlInNewTab(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([Tables\Actions\DeleteBulkAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCutPlan::route('/'),
            'create' => CreateCutPlan::route('/create'),
            'edit' => EditCutPlan::route('/{record}/edit'),
        ];
    }
}

class ListCutPlan extends ListRecords
{
    protected static string $resource = CutPlanResource::class;

    protected function getHeaderActions(): array
    {
        return [\Filament\Actions\CreateAction::make()];
    }
}

class CreateCutPlan extends CreateRecord
{
    protected static string $resource = CutPlanResource::class;
}

class EditCutPlan extends EditRecord
{
    protected static string $resource = CutPlanResource::class;
}
