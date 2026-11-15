<?php

namespace App\Filament\Resources;

use App\Filament\Support\FieldBuilder;
use App\Filament\Support\GuardsModule;
use App\Models\Shipment;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ShipmentResource extends Resource
{
    use GuardsModule;

    protected static ?string $model = Shipment::class;

    protected static ?string $navigationIcon = 'heroicon-o-paper-airplane';

    protected static ?string $navigationGroup = 'Wash & Pack';

    protected static ?int $navigationSort = 5;

    protected static ?string $navigationLabel = 'Shipment';

    public static function module(): string
    {
        return 'shipment';
    }

    public static function roles(): array
    {
        return [
    'merchandiser',
    'buyer',
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
                'name' => 'buyer_order_id',
                'type' => 'relation',
                'relation' => 'buyerOrder',
                'title' => 'po_number',
                'required' => true,
                'filter' => true,
            ],
            [
                'name' => 'shipment_no',
                'type' => 'text',
                'required' => true,
                'search' => true,
            ],
            [
                'name' => 'mode',
                'type' => 'select',
                'options' => [
                    'sea' => 'Sea',
                    'air' => 'Air',
                    'road' => 'Road',
                ],
                'default' => 'sea',
                'filter' => true,
            ],
            [
                'name' => 'container_no',
                'type' => 'text',
            ],
            [
                'name' => 'vessel',
                'type' => 'text',
            ],
            [
                'name' => 'etd',
                'type' => 'date',
                'label' => 'ETD',
            ],
            [
                'name' => 'eta',
                'type' => 'date',
                'label' => 'ETA',
            ],
            [
                'name' => 'status',
                'type' => 'select',
                'options' => [
                    'planned' => 'Planned',
                    'loaded' => 'Loaded',
                    'in_transit' => 'In transit',
                    'delivered' => 'Delivered',
                ],
                'default' => 'planned',
                'filter' => true,
                'badge' => true,
            ],
            [
                'name' => 'invoice_no',
                'type' => 'text',
            ],
            [
                'name' => 'destination',
                'type' => 'text',
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
                Tables\Actions\Action::make('invoice')->label('Invoice')->url(fn ($record) => route('pdf.invoice', $record))->openUrlInNewTab(),
                Tables\Actions\Action::make('packing')->label('Packing list')->url(fn ($record) => route('pdf.packing-list', $record))->openUrlInNewTab(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([Tables\Actions\DeleteBulkAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListShipment::route('/'),
            'create' => CreateShipment::route('/create'),
            'edit' => EditShipment::route('/{record}/edit'),
        ];
    }
}

class ListShipment extends ListRecords
{
    protected static string $resource = ShipmentResource::class;

    protected function getHeaderActions(): array
    {
        return [\Filament\Actions\CreateAction::make()];
    }
}

class CreateShipment extends CreateRecord
{
    protected static string $resource = ShipmentResource::class;
}

class EditShipment extends EditRecord
{
    protected static string $resource = ShipmentResource::class;
}
