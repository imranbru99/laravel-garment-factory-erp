<?php

namespace App\Filament\Resources;

use App\Filament\Support\FieldBuilder;
use App\Filament\Support\GuardsModule;
use App\Models\StockReservation;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class StockReservationResource extends Resource
{
    use GuardsModule;

    protected static ?string $model = StockReservation::class;

    protected static ?string $navigationIcon = 'heroicon-o-lock-closed';

    protected static ?string $navigationGroup = 'Materials';

    protected static ?int $navigationSort = 10;

    protected static ?string $navigationLabel = 'StockReservation';

    public static function module(): string
    {
        return 'inventory';
    }

    public static function roles(): array
    {
        return [
    'store',
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
                'name' => 'reservable_type',
                'type' => 'text',
                'required' => true,
            ],
            [
                'name' => 'reservable_id',
                'type' => 'number',
                'required' => true,
            ],
            [
                'name' => 'quantity',
                'type' => 'decimal',
                'required' => true,
            ],
            [
                'name' => 'status',
                'type' => 'select',
                'options' => [
                    'reserved' => 'Reserved',
                    'issued' => 'Issued',
                    'released' => 'Released',
                ],
                'default' => 'reserved',
                'filter' => true,
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
            'index' => ListStockReservation::route('/'),
            'create' => CreateStockReservation::route('/create'),
            'edit' => EditStockReservation::route('/{record}/edit'),
        ];
    }
}

class ListStockReservation extends ListRecords
{
    protected static string $resource = StockReservationResource::class;

    protected function getHeaderActions(): array
    {
        return [\Filament\Actions\CreateAction::make()];
    }
}

class CreateStockReservation extends CreateRecord
{
    protected static string $resource = StockReservationResource::class;
}

class EditStockReservation extends EditRecord
{
    protected static string $resource = StockReservationResource::class;
}
