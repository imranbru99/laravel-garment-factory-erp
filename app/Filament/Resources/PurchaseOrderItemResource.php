<?php

namespace App\Filament\Resources;

use App\Filament\Support\FieldBuilder;
use App\Filament\Support\GuardsModule;
use App\Models\PurchaseOrderItem;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PurchaseOrderItemResource extends Resource
{
    use GuardsModule;

    protected static ?string $model = PurchaseOrderItem::class;

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

    protected static ?string $navigationGroup = 'Materials';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'PurchaseOrderItem';

    public static function module(): string
    {
        return 'procurement';
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
                'name' => 'purchase_order_id',
                'type' => 'relation',
                'relation' => 'purchaseOrder',
                'title' => 'po_number',
                'required' => true,
                'filter' => true,
            ],
            [
                'name' => 'description',
                'type' => 'text',
                'required' => true,
                'search' => true,
            ],
            [
                'name' => 'color',
                'type' => 'text',
            ],
            [
                'name' => 'spec',
                'type' => 'text',
            ],
            [
                'name' => 'quantity',
                'type' => 'decimal',
                'required' => true,
            ],
            [
                'name' => 'uom',
                'type' => 'text',
                'default' => 'm',
            ],
            [
                'name' => 'unit_price',
                'type' => 'decimal',
            ],
            [
                'name' => 'received_qty',
                'type' => 'decimal',
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
            'index' => ListPurchaseOrderItem::route('/'),
            'create' => CreatePurchaseOrderItem::route('/create'),
            'edit' => EditPurchaseOrderItem::route('/{record}/edit'),
        ];
    }
}

class ListPurchaseOrderItem extends ListRecords
{
    protected static string $resource = PurchaseOrderItemResource::class;

    protected function getHeaderActions(): array
    {
        return [\Filament\Actions\CreateAction::make()];
    }
}

class CreatePurchaseOrderItem extends CreateRecord
{
    protected static string $resource = PurchaseOrderItemResource::class;
}

class EditPurchaseOrderItem extends EditRecord
{
    protected static string $resource = PurchaseOrderItemResource::class;
}
