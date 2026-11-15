<?php

namespace App\Filament\Resources;

use App\Filament\Support\FieldBuilder;
use App\Filament\Support\GuardsModule;
use App\Models\StockMovement;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class StockMovementResource extends Resource
{
    use GuardsModule;

    protected static ?string $model = StockMovement::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrows-right-left';

    protected static ?string $navigationGroup = 'Materials';

    protected static ?int $navigationSort = 9;

    protected static ?string $navigationLabel = 'StockMovement';

    public static function module(): string
    {
        return 'inventory';
    }

    public static function roles(): array
    {
        return [
    'store',
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
                'name' => 'movable_type',
                'type' => 'text',
                'required' => true,
            ],
            [
                'name' => 'movable_id',
                'type' => 'number',
                'required' => true,
            ],
            [
                'name' => 'buyer_order_id',
                'type' => 'relation',
                'relation' => 'buyerOrder',
                'title' => 'po_number',
            ],
            [
                'name' => 'type',
                'type' => 'select',
                'options' => [
                    'in' => 'In',
                    'out' => 'Out',
                    'reserve' => 'Reserve',
                    'release' => 'Release',
                    'adjust' => 'Adjust',
                ],
                'required' => true,
                'filter' => true,
                'badge' => true,
            ],
            [
                'name' => 'quantity',
                'type' => 'decimal',
                'required' => true,
            ],
            [
                'name' => 'reference',
                'type' => 'text',
            ],
            [
                'name' => 'note',
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
            'index' => ListStockMovement::route('/'),
            'create' => CreateStockMovement::route('/create'),
            'edit' => EditStockMovement::route('/{record}/edit'),
        ];
    }
}

class ListStockMovement extends ListRecords
{
    protected static string $resource = StockMovementResource::class;

    protected function getHeaderActions(): array
    {
        return [\Filament\Actions\CreateAction::make()];
    }
}

class CreateStockMovement extends CreateRecord
{
    protected static string $resource = StockMovementResource::class;
}

class EditStockMovement extends EditRecord
{
    protected static string $resource = StockMovementResource::class;
}
