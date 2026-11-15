<?php

namespace App\Filament\Resources;

use App\Filament\Support\FieldBuilder;
use App\Filament\Support\GuardsModule;
use App\Models\Department;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DepartmentResource extends Resource
{
    use GuardsModule;

    protected static ?string $model = Department::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-group';

    protected static ?string $navigationGroup = 'Organization';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'Department';

    public static function module(): string
    {
        return 'organization';
    }

    public static function roles(): array
    {
        return [
    'admin',
    'production_manager',
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
                'name' => 'type',
                'type' => 'select',
                'options' => [
                    'merchandising' => 'Merchandising',
                    'store' => 'Store',
                    'cutting' => 'Cutting',
                    'sewing' => 'Sewing',
                    'washing' => 'Washing',
                    'finishing' => 'Finishing',
                    'packing' => 'Packing',
                    'qc' => 'QC',
                    'maintenance' => 'Maintenance',
                    'hr' => 'HR',
                ],
                'required' => true,
                'filter' => true,
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
            'index' => ListDepartment::route('/'),
            'create' => CreateDepartment::route('/create'),
            'edit' => EditDepartment::route('/{record}/edit'),
        ];
    }
}

class ListDepartment extends ListRecords
{
    protected static string $resource = DepartmentResource::class;

    protected function getHeaderActions(): array
    {
        return [\Filament\Actions\CreateAction::make()];
    }
}

class CreateDepartment extends CreateRecord
{
    protected static string $resource = DepartmentResource::class;
}

class EditDepartment extends EditRecord
{
    protected static string $resource = DepartmentResource::class;
}
