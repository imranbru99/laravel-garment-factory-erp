<?php

namespace App\Filament\Resources;

use App\Filament\Support\FieldBuilder;
use App\Filament\Support\GuardsModule;
use App\Models\OrderMatrixLine;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class OrderMatrixLineResource extends Resource
{
    use GuardsModule;

    protected static ?string $model = OrderMatrixLine::class;

    protected static ?string $navigationIcon = 'heroicon-o-table-cells';

    protected static ?string $navigationGroup = 'Merchandising';

    protected static ?int $navigationSort = 8;

    protected static ?string $navigationLabel = 'Size / colour matrix';

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
                'name' => 'buyer_order_id',
                'type' => 'relation',
                'relation' => 'buyerOrder',
                'title' => 'po_number',
                'required' => true,
                'filter' => true,
            ],
            [
                'name' => 'color',
                'type' => 'text',
                'required' => true,
                'search' => true,
                'filter' => true,
            ],
            [
                'name' => 'size',
                'type' => 'select',
                'options' => 'sizes',
                'required' => true,
                'filter' => true,
            ],
            [
                'name' => 'quantity',
                'type' => 'number',
                'required' => true,
            ],
            [
                'name' => 'cut_qty',
                'type' => 'number',
            ],
            [
                'name' => 'sewn_qty',
                'type' => 'number',
            ],
            [
                'name' => 'packed_qty',
                'type' => 'number',
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
            'index' => ListOrderMatrixLine::route('/'),
            'create' => CreateOrderMatrixLine::route('/create'),
            'edit' => EditOrderMatrixLine::route('/{record}/edit'),
        ];
    }
}

class ListOrderMatrixLine extends ListRecords
{
    protected static string $resource = OrderMatrixLineResource::class;

    protected function getHeaderActions(): array
    {
        return [\Filament\Actions\CreateAction::make()];
    }
}

class CreateOrderMatrixLine extends CreateRecord
{
    protected static string $resource = OrderMatrixLineResource::class;
}

class EditOrderMatrixLine extends EditRecord
{
    protected static string $resource = OrderMatrixLineResource::class;
}
