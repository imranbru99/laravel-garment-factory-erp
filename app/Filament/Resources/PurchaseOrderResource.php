<?php

namespace App\Filament\Resources;

use App\Filament\Support\FieldBuilder;
use App\Filament\Support\GuardsModule;
use App\Models\PurchaseOrder;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PurchaseOrderResource extends Resource
{
    use GuardsModule;

    protected static ?string $model = PurchaseOrder::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-currency-dollar';

    protected static ?string $navigationGroup = 'Materials';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'PurchaseOrder';

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
                'name' => 'factory_id',
                'type' => 'relation',
                'relation' => 'factory',
                'title' => 'name',
                'required' => true,
                'filter' => true,
            ],
            [
                'name' => 'supplier_id',
                'type' => 'relation',
                'relation' => 'supplier',
                'title' => 'name',
                'required' => true,
                'filter' => true,
            ],
            [
                'name' => 'buyer_order_id',
                'type' => 'relation',
                'relation' => 'buyerOrder',
                'title' => 'po_number',
            ],
            [
                'name' => 'po_number',
                'type' => 'text',
                'required' => true,
                'search' => true,
            ],
            [
                'name' => 'type',
                'type' => 'select',
                'options' => [
                    'fabric' => 'Fabric',
                    'trim' => 'Trim',
                ],
                'required' => true,
                'filter' => true,
            ],
            [
                'name' => 'status',
                'type' => 'select',
                'options' => 'po_statuses',
                'default' => 'draft',
                'filter' => true,
                'badge' => true,
            ],
            [
                'name' => 'order_date',
                'type' => 'date',
                'required' => true,
            ],
            [
                'name' => 'expected_date',
                'type' => 'date',
            ],
            [
                'name' => 'currency',
                'type' => 'text',
                'default' => 'USD',
            ],
            [
                'name' => 'total',
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
            'index' => ListPurchaseOrder::route('/'),
            'create' => CreatePurchaseOrder::route('/create'),
            'edit' => EditPurchaseOrder::route('/{record}/edit'),
        ];
    }
}

class ListPurchaseOrder extends ListRecords
{
    protected static string $resource = PurchaseOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [\Filament\Actions\CreateAction::make()];
    }
}

class CreatePurchaseOrder extends CreateRecord
{
    protected static string $resource = PurchaseOrderResource::class;
}

class EditPurchaseOrder extends EditRecord
{
    protected static string $resource = PurchaseOrderResource::class;
}
