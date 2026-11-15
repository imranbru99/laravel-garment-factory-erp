<?php

namespace App\Filament\Resources;

use App\Filament\Support\FieldBuilder;
use App\Filament\Support\GuardsModule;
use App\Models\FactoryHoliday;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class FactoryHolidayResource extends Resource
{
    use GuardsModule;

    protected static ?string $model = FactoryHoliday::class;

    protected static ?string $navigationIcon = 'heroicon-o-sun';

    protected static ?string $navigationGroup = 'Organization';

    protected static ?int $navigationSort = 7;

    protected static ?string $navigationLabel = 'FactoryHoliday';

    public static function module(): string
    {
        return 'hr';
    }

    public static function roles(): array
    {
        return [
    'hr',
    'production_manager',
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
            ],
            [
                'name' => 'holiday_date',
                'type' => 'date',
                'required' => true,
            ],
            [
                'name' => 'name',
                'type' => 'text',
                'required' => true,
                'search' => true,
            ],
            [
                'name' => 'region',
                'type' => 'text',
                'default' => 'BD',
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
            'index' => ListFactoryHoliday::route('/'),
            'create' => CreateFactoryHoliday::route('/create'),
            'edit' => EditFactoryHoliday::route('/{record}/edit'),
        ];
    }
}

class ListFactoryHoliday extends ListRecords
{
    protected static string $resource = FactoryHolidayResource::class;

    protected function getHeaderActions(): array
    {
        return [\Filament\Actions\CreateAction::make()];
    }
}

class CreateFactoryHoliday extends CreateRecord
{
    protected static string $resource = FactoryHolidayResource::class;
}

class EditFactoryHoliday extends EditRecord
{
    protected static string $resource = FactoryHolidayResource::class;
}
