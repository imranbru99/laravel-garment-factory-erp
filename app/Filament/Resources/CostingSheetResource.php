<?php

namespace App\Filament\Resources;

use App\Filament\Support\FieldBuilder;
use App\Filament\Support\GuardsModule;
use App\Models\CostingSheet;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CostingSheetResource extends Resource
{
    use GuardsModule;

    protected static ?string $model = CostingSheet::class;

    protected static ?string $navigationIcon = 'heroicon-o-calculator';

    protected static ?string $navigationGroup = 'Merchandising';

    protected static ?int $navigationSort = 10;

    protected static ?string $navigationLabel = 'CostingSheet';

    public static function module(): string
    {
        return 'orders';
    }

    public static function roles(): array
    {
        return [
    'merchandiser',
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
                'name' => 'buyer_order_id',
                'type' => 'relation',
                'relation' => 'buyerOrder',
                'title' => 'po_number',
                'required' => true,
                'filter' => true,
            ],
            [
                'name' => 'style_id',
                'type' => 'relation',
                'relation' => 'style',
                'title' => 'name',
                'required' => true,
            ],
            [
                'name' => 'smv',
                'type' => 'decimal',
            ],
            [
                'name' => 'cm_cost',
                'type' => 'decimal',
                'label' => 'CM',
            ],
            [
                'name' => 'fabric_cost',
                'type' => 'decimal',
            ],
            [
                'name' => 'trim_cost',
                'type' => 'decimal',
            ],
            [
                'name' => 'wash_cost',
                'type' => 'decimal',
            ],
            [
                'name' => 'overhead',
                'type' => 'decimal',
            ],
            [
                'name' => 'fob',
                'type' => 'decimal',
                'label' => 'FOB',
            ],
            [
                'name' => 'margin_percent',
                'type' => 'decimal',
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
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()])
            ->bulkActions([Tables\Actions\DeleteBulkAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCostingSheet::route('/'),
            'create' => CreateCostingSheet::route('/create'),
            'edit' => EditCostingSheet::route('/{record}/edit'),
        ];
    }
}

class ListCostingSheet extends ListRecords
{
    protected static string $resource = CostingSheetResource::class;

    protected function getHeaderActions(): array
    {
        return [\Filament\Actions\CreateAction::make()];
    }
}

class CreateCostingSheet extends CreateRecord
{
    protected static string $resource = CostingSheetResource::class;
}

class EditCostingSheet extends EditRecord
{
    protected static string $resource = CostingSheetResource::class;
}
