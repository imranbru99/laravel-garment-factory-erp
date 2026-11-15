<?php

namespace App\Filament\Resources;

use App\Filament\Support\FieldBuilder;
use App\Filament\Support\GuardsModule;
use App\Models\SewingLine;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SewingLineResource extends Resource
{
    use GuardsModule;

    protected static ?string $model = SewingLine::class;

    protected static ?string $navigationIcon = 'heroicon-o-queue-list';

    protected static ?string $navigationGroup = 'Organization';

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationLabel = 'SewingLine';

    public static function module(): string
    {
        return 'planning';
    }

    public static function roles(): array
    {
        return [
    'production_manager',
    'cutting_master',
    'hr',
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
                'name' => 'department_id',
                'type' => 'relation',
                'relation' => 'department',
                'title' => 'name',
            ],
            [
                'name' => 'code',
                'type' => 'text',
                'required' => true,
                'search' => true,
            ],
            [
                'name' => 'name',
                'type' => 'text',
                'required' => true,
                'search' => true,
            ],
            [
                'name' => 'operators_count',
                'type' => 'number',
                'default' => 20,
            ],
            [
                'name' => 'target_efficiency',
                'type' => 'decimal',
                'default' => 75,
            ],
            [
                'name' => 'hourly_target',
                'type' => 'number',
                'default' => 40,
            ],
            [
                'name' => 'is_active',
                'type' => 'toggle',
                'default' => true,
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
            'index' => ListSewingLine::route('/'),
            'create' => CreateSewingLine::route('/create'),
            'edit' => EditSewingLine::route('/{record}/edit'),
        ];
    }
}

class ListSewingLine extends ListRecords
{
    protected static string $resource = SewingLineResource::class;

    protected function getHeaderActions(): array
    {
        return [\Filament\Actions\CreateAction::make()];
    }
}

class CreateSewingLine extends CreateRecord
{
    protected static string $resource = SewingLineResource::class;
}

class EditSewingLine extends EditRecord
{
    protected static string $resource = SewingLineResource::class;
}
