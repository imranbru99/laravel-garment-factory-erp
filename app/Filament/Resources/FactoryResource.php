<?php

namespace App\Filament\Resources;

use App\Filament\Support\FieldBuilder;
use App\Filament\Support\GuardsModule;
use App\Models\Factory;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class FactoryResource extends Resource
{
    use GuardsModule;

    protected static ?string $model = Factory::class;

    protected static ?string $navigationIcon = 'heroicon-o-home-modern';

    protected static ?string $navigationGroup = 'Organization';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Factory';

    public static function module(): string
    {
        return 'organization';
    }

    public static function roles(): array
    {
        return [
    'admin',
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
                'name' => 'company_id',
                'type' => 'relation',
                'relation' => 'company',
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
                'name' => 'code',
                'type' => 'text',
                'required' => true,
            ],
            [
                'name' => 'city',
                'type' => 'text',
            ],
            [
                'name' => 'sewing_lines',
                'type' => 'number',
            ],
            [
                'name' => 'daily_capacity_pcs',
                'type' => 'number',
            ],
            [
                'name' => 'is_active',
                'type' => 'toggle',
                'default' => true,
            ],
            [
                'name' => 'address',
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
            'index' => ListFactory::route('/'),
            'create' => CreateFactory::route('/create'),
            'edit' => EditFactory::route('/{record}/edit'),
        ];
    }
}

class ListFactory extends ListRecords
{
    protected static string $resource = FactoryResource::class;

    protected function getHeaderActions(): array
    {
        return [\Filament\Actions\CreateAction::make()];
    }
}

class CreateFactory extends CreateRecord
{
    protected static string $resource = FactoryResource::class;
}

class EditFactory extends EditRecord
{
    protected static string $resource = FactoryResource::class;
}
