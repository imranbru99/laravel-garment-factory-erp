<?php

namespace App\Filament\Resources;

use App\Filament\Support\FieldBuilder;
use App\Filament\Support\GuardsModule;
use App\Models\Operation;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class OperationResource extends Resource
{
    use GuardsModule;

    protected static ?string $model = Operation::class;

    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';

    protected static ?string $navigationGroup = 'Sewing Floor';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Operation';

    public static function module(): string
    {
        return 'sewing';
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
                'name' => 'section',
                'type' => 'select',
                'options' => 'sections',
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
                'name' => 'sequence',
                'type' => 'number',
                'default' => 1,
            ],
            [
                'name' => 'smv',
                'type' => 'decimal',
            ],
            [
                'name' => 'machine_type',
                'type' => 'text',
            ],
            [
                'name' => 'piece_rate',
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
            'index' => ListOperation::route('/'),
            'create' => CreateOperation::route('/create'),
            'edit' => EditOperation::route('/{record}/edit'),
        ];
    }
}

class ListOperation extends ListRecords
{
    protected static string $resource = OperationResource::class;

    protected function getHeaderActions(): array
    {
        return [\Filament\Actions\CreateAction::make()];
    }
}

class CreateOperation extends CreateRecord
{
    protected static string $resource = OperationResource::class;
}

class EditOperation extends EditRecord
{
    protected static string $resource = OperationResource::class;
}
