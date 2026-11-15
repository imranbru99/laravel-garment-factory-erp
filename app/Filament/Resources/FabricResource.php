<?php

namespace App\Filament\Resources;

use App\Filament\Support\FieldBuilder;
use App\Filament\Support\GuardsModule;
use App\Models\Fabric;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class FabricResource extends Resource
{
    use GuardsModule;

    protected static ?string $model = Fabric::class;

    protected static ?string $navigationIcon = 'heroicon-o-square-3-stack-3d';

    protected static ?string $navigationGroup = 'Materials';

    protected static ?int $navigationSort = 6;

    protected static ?string $navigationLabel = 'Fabric';

    public static function module(): string
    {
        return 'inventory';
    }

    public static function roles(): array
    {
        return [
    'store',
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
                'name' => 'name',
                'type' => 'text',
                'required' => true,
                'search' => true,
            ],
            [
                'name' => 'composition',
                'type' => 'text',
            ],
            [
                'name' => 'gsm',
                'type' => 'number',
            ],
            [
                'name' => 'width_inch',
                'type' => 'decimal',
                'label' => 'Width (inch)',
            ],
            [
                'name' => 'color',
                'type' => 'text',
                'search' => true,
            ],
            [
                'name' => 'uom',
                'type' => 'text',
                'default' => 'm',
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
            'index' => ListFabric::route('/'),
            'create' => CreateFabric::route('/create'),
            'edit' => EditFabric::route('/{record}/edit'),
        ];
    }
}

class ListFabric extends ListRecords
{
    protected static string $resource = FabricResource::class;

    protected function getHeaderActions(): array
    {
        return [\Filament\Actions\CreateAction::make()];
    }
}

class CreateFabric extends CreateRecord
{
    protected static string $resource = FabricResource::class;
}

class EditFabric extends EditRecord
{
    protected static string $resource = FabricResource::class;
}
