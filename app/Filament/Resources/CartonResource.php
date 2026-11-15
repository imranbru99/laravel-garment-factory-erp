<?php

namespace App\Filament\Resources;

use App\Filament\Support\FieldBuilder;
use App\Filament\Support\GuardsModule;
use App\Models\Carton;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CartonResource extends Resource
{
    use GuardsModule;

    protected static ?string $model = Carton::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';

    protected static ?string $navigationGroup = 'Wash & Pack';

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationLabel = 'Carton';

    public static function module(): string
    {
        return 'packing';
    }

    public static function roles(): array
    {
        return [
    'production_manager',
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
                'name' => 'shipment_id',
                'type' => 'relation',
                'relation' => 'shipment',
                'title' => 'shipment_no',
            ],
            [
                'name' => 'carton_no',
                'type' => 'text',
                'required' => true,
                'search' => true,
            ],
            [
                'name' => 'color',
                'type' => 'text',
            ],
            [
                'name' => 'size',
                'type' => 'text',
            ],
            [
                'name' => 'quantity',
                'type' => 'number',
                'required' => true,
            ],
            [
                'name' => 'gross_weight',
                'type' => 'decimal',
            ],
            [
                'name' => 'net_weight',
                'type' => 'decimal',
            ],
            [
                'name' => 'barcode',
                'type' => 'text',
            ],
            [
                'name' => 'status',
                'type' => 'select',
                'options' => [
                    'open' => 'Open',
                    'packed' => 'Packed',
                    'loaded' => 'Loaded',
                ],
                'default' => 'open',
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
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()])
            ->bulkActions([Tables\Actions\DeleteBulkAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCarton::route('/'),
            'create' => CreateCarton::route('/create'),
            'edit' => EditCarton::route('/{record}/edit'),
        ];
    }
}

class ListCarton extends ListRecords
{
    protected static string $resource = CartonResource::class;

    protected function getHeaderActions(): array
    {
        return [\Filament\Actions\CreateAction::make()];
    }
}

class CreateCarton extends CreateRecord
{
    protected static string $resource = CartonResource::class;
}

class EditCarton extends EditRecord
{
    protected static string $resource = CartonResource::class;
}
