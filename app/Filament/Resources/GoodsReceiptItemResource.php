<?php

namespace App\Filament\Resources;

use App\Filament\Support\FieldBuilder;
use App\Filament\Support\GuardsModule;
use App\Models\GoodsReceiptItem;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class GoodsReceiptItemResource extends Resource
{
    use GuardsModule;

    protected static ?string $model = GoodsReceiptItem::class;

    protected static ?string $navigationIcon = 'heroicon-o-inbox-stack';

    protected static ?string $navigationGroup = 'Materials';

    protected static ?int $navigationSort = 5;

    protected static ?string $navigationLabel = 'GoodsReceiptItem';

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
                'name' => 'goods_receipt_id',
                'type' => 'relation',
                'relation' => 'goodsReceipt',
                'title' => 'grn_number',
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
                'name' => 'quantity',
                'type' => 'decimal',
                'required' => true,
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
                'name' => 'accepted_qty',
                'type' => 'decimal',
            ],
            [
                'name' => 'rejected_qty',
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
            'index' => ListGoodsReceiptItem::route('/'),
            'create' => CreateGoodsReceiptItem::route('/create'),
            'edit' => EditGoodsReceiptItem::route('/{record}/edit'),
        ];
    }
}

class ListGoodsReceiptItem extends ListRecords
{
    protected static string $resource = GoodsReceiptItemResource::class;

    protected function getHeaderActions(): array
    {
        return [\Filament\Actions\CreateAction::make()];
    }
}

class CreateGoodsReceiptItem extends CreateRecord
{
    protected static string $resource = GoodsReceiptItemResource::class;
}

class EditGoodsReceiptItem extends EditRecord
{
    protected static string $resource = GoodsReceiptItemResource::class;
}
