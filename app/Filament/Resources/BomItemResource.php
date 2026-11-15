<?php

namespace App\Filament\Resources;

use App\Filament\Support\FieldBuilder;
use App\Filament\Support\GuardsModule;
use App\Models\BomItem;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BomItemResource extends Resource
{
    use GuardsModule;

    protected static ?string $model = BomItem::class;

    protected static ?string $navigationIcon = 'heroicon-o-list-bullet';

    protected static ?string $navigationGroup = 'Merchandising';

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationLabel = 'BomItem';

    public static function module(): string
    {
        return 'styles';
    }

    public static function roles(): array
    {
        return [
    'merchandiser',
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
                'name' => 'tech_pack_id',
                'type' => 'relation',
                'relation' => 'techPack',
                'title' => 'version',
                'required' => true,
                'filter' => true,
            ],
            [
                'name' => 'item_type',
                'type' => 'select',
                'options' => [
                    'fabric' => 'Fabric',
                    'trim' => 'Trim',
                ],
                'required' => true,
                'filter' => true,
            ],
            [
                'name' => 'name',
                'type' => 'text',
                'required' => true,
                'search' => true,
            ],
            [
                'name' => 'spec',
                'type' => 'text',
            ],
            [
                'name' => 'consumption',
                'type' => 'decimal',
            ],
            [
                'name' => 'uom',
                'type' => 'text',
                'default' => 'pcs',
            ],
            [
                'name' => 'unit_cost',
                'type' => 'decimal',
            ],
            [
                'name' => 'supplier_id',
                'type' => 'relation',
                'relation' => 'supplier',
                'title' => 'name',
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
            'index' => ListBomItem::route('/'),
            'create' => CreateBomItem::route('/create'),
            'edit' => EditBomItem::route('/{record}/edit'),
        ];
    }
}

class ListBomItem extends ListRecords
{
    protected static string $resource = BomItemResource::class;

    protected function getHeaderActions(): array
    {
        return [\Filament\Actions\CreateAction::make()];
    }
}

class CreateBomItem extends CreateRecord
{
    protected static string $resource = BomItemResource::class;
}

class EditBomItem extends EditRecord
{
    protected static string $resource = BomItemResource::class;
}
