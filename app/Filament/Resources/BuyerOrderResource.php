<?php

namespace App\Filament\Resources;

use App\Filament\Support\FieldBuilder;
use App\Filament\Support\GuardsModule;
use App\Models\BuyerOrder;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BuyerOrderResource extends Resource
{
    use GuardsModule;

    protected static ?string $model = BuyerOrder::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationGroup = 'Merchandising';

    protected static ?int $navigationSort = 7;

    protected static ?string $navigationLabel = 'Buyer POs';

    public static function module(): string
    {
        return 'orders';
    }

    public static function roles(): array
    {
        return [
    'merchandiser',
    'buyer',
    'production_manager',
    'cutting_master',
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
                'name' => 'buyer_id',
                'type' => 'relation',
                'relation' => 'buyer',
                'title' => 'name',
                'required' => true,
                'filter' => true,
            ],
            [
                'name' => 'style_id',
                'type' => 'relation',
                'relation' => 'style',
                'title' => 'name',
                'required' => true,
                'filter' => true,
            ],
            [
                'name' => 'po_number',
                'type' => 'text',
                'required' => true,
                'search' => true,
                'label' => 'PO number',
            ],
            [
                'name' => 'season',
                'type' => 'text',
            ],
            [
                'name' => 'order_qty',
                'type' => 'number',
            ],
            [
                'name' => 'fob_price',
                'type' => 'decimal',
                'label' => 'FOB',
            ],
            [
                'name' => 'currency',
                'type' => 'text',
                'default' => 'USD',
            ],
            [
                'name' => 'ex_factory_date',
                'type' => 'date',
                'label' => 'Ex-factory',
            ],
            [
                'name' => 'priority',
                'type' => 'select',
                'options' => 'priorities',
                'default' => 'normal',
                'filter' => true,
                'badge' => true,
            ],
            [
                'name' => 'status',
                'type' => 'select',
                'options' => 'order_statuses',
                'default' => 'draft',
                'filter' => true,
                'badge' => true,
            ],
            [
                'name' => 'remarks',
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
            'index' => ListBuyerOrder::route('/'),
            'create' => CreateBuyerOrder::route('/create'),
            'edit' => EditBuyerOrder::route('/{record}/edit'),
        ];
    }
}

class ListBuyerOrder extends ListRecords
{
    protected static string $resource = BuyerOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [\Filament\Actions\CreateAction::make()];
    }
}

class CreateBuyerOrder extends CreateRecord
{
    protected static string $resource = BuyerOrderResource::class;
}

class EditBuyerOrder extends EditRecord
{
    protected static string $resource = BuyerOrderResource::class;
}
