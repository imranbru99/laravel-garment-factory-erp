<?php

namespace App\Filament\Resources;

use App\Filament\Support\FieldBuilder;
use App\Filament\Support\GuardsModule;
use App\Models\Machine;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class MachineResource extends Resource
{
    use GuardsModule;

    protected static ?string $model = Machine::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationGroup = 'Maintenance';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Machine';

    public static function module(): string
    {
        return 'maintenance';
    }

    public static function roles(): array
    {
        return [
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
                'required' => true,
            ],
            [
                'name' => 'sewing_line_id',
                'type' => 'relation',
                'relation' => 'sewingLine',
                'title' => 'code',
                'filter' => true,
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
                'name' => 'type',
                'type' => 'text',
            ],
            [
                'name' => 'brand',
                'type' => 'text',
            ],
            [
                'name' => 'status',
                'type' => 'select',
                'options' => 'machine_statuses',
                'default' => 'running',
                'filter' => true,
                'badge' => true,
            ],
            [
                'name' => 'last_service_on',
                'type' => 'date',
            ],
            [
                'name' => 'next_service_on',
                'type' => 'date',
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
            'index' => ListMachine::route('/'),
            'create' => CreateMachine::route('/create'),
            'edit' => EditMachine::route('/{record}/edit'),
        ];
    }
}

class ListMachine extends ListRecords
{
    protected static string $resource = MachineResource::class;

    protected function getHeaderActions(): array
    {
        return [\Filament\Actions\CreateAction::make()];
    }
}

class CreateMachine extends CreateRecord
{
    protected static string $resource = MachineResource::class;
}

class EditMachine extends EditRecord
{
    protected static string $resource = MachineResource::class;
}
