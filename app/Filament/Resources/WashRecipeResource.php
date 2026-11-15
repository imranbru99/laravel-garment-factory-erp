<?php

namespace App\Filament\Resources;

use App\Filament\Support\FieldBuilder;
use App\Filament\Support\GuardsModule;
use App\Models\WashRecipe;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class WashRecipeResource extends Resource
{
    use GuardsModule;

    protected static ?string $model = WashRecipe::class;

    protected static ?string $navigationIcon = 'heroicon-o-beaker';

    protected static ?string $navigationGroup = 'Wash & Pack';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'WashRecipe';

    public static function module(): string
    {
        return 'washing';
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
                'name' => 'style_id',
                'type' => 'relation',
                'relation' => 'style',
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
                'name' => 'process',
                'type' => 'text',
            ],
            [
                'name' => 'temperature_c',
                'type' => 'decimal',
                'label' => 'Temperature °C',
            ],
            [
                'name' => 'duration_min',
                'type' => 'number',
            ],
            [
                'name' => 'chemicals',
                'type' => 'keyvalue',
            ],
            [
                'name' => 'notes',
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
            'index' => ListWashRecipe::route('/'),
            'create' => CreateWashRecipe::route('/create'),
            'edit' => EditWashRecipe::route('/{record}/edit'),
        ];
    }
}

class ListWashRecipe extends ListRecords
{
    protected static string $resource = WashRecipeResource::class;

    protected function getHeaderActions(): array
    {
        return [\Filament\Actions\CreateAction::make()];
    }
}

class CreateWashRecipe extends CreateRecord
{
    protected static string $resource = WashRecipeResource::class;
}

class EditWashRecipe extends EditRecord
{
    protected static string $resource = WashRecipeResource::class;
}
