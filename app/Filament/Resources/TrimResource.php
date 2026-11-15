<?php

namespace App\Filament\Resources;

use App\Filament\Support\FieldBuilder;
use App\Filament\Support\GuardsModule;
use App\Models\Trim;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TrimResource extends Resource
{
    use GuardsModule;

    protected static ?string $model = Trim::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?string $navigationGroup = 'Materials';

    protected static ?int $navigationSort = 8;

    protected static ?string $navigationLabel = 'Trim';

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
                'name' => 'name',
                'type' => 'text',
                'required' => true,
                'search' => true,
            ],
            [
                'name' => 'category',
                'type' => 'select',
                'options' => 'trim_categories',
                'required' => true,
                'filter' => true,
                'badge' => true,
            ],
            [
                'name' => 'spec',
                'type' => 'text',
            ],
            [
                'name' => 'color',
                'type' => 'text',
            ],
            [
                'name' => 'uom',
                'type' => 'text',
                'default' => 'pcs',
            ],
            [
                'name' => 'stock_qty',
                'type' => 'decimal',
            ],
            [
                'name' => 'reorder_level',
                'type' => 'decimal',
            ],
            [
                'name' => 'unit_cost',
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
            'index' => ListTrim::route('/'),
            'create' => CreateTrim::route('/create'),
            'edit' => EditTrim::route('/{record}/edit'),
        ];
    }
}

class ListTrim extends ListRecords
{
    protected static string $resource = TrimResource::class;

    protected function getHeaderActions(): array
    {
        return [\Filament\Actions\CreateAction::make()];
    }
}

class CreateTrim extends CreateRecord
{
    protected static string $resource = TrimResource::class;
}

class EditTrim extends EditRecord
{
    protected static string $resource = TrimResource::class;
}
