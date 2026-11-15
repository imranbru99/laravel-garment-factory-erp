<?php

namespace App\Filament\Resources;

use App\Filament\Support\FieldBuilder;
use App\Filament\Support\GuardsModule;
use App\Models\Employee;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class EmployeeResource extends Resource
{
    use GuardsModule;

    protected static ?string $model = Employee::class;

    protected static ?string $navigationIcon = 'heroicon-o-identification';

    protected static ?string $navigationGroup = 'People';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Employee';

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
                'name' => 'sewing_line_id',
                'type' => 'relation',
                'relation' => 'sewingLine',
                'title' => 'code',
                'filter' => true,
            ],
            [
                'name' => 'shift_id',
                'type' => 'relation',
                'relation' => 'shift',
                'title' => 'name',
            ],
            [
                'name' => 'employee_code',
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
                'name' => 'designation',
                'type' => 'text',
            ],
            [
                'name' => 'skill_grade',
                'type' => 'text',
            ],
            [
                'name' => 'piece_rate',
                'type' => 'decimal',
            ],
            [
                'name' => 'hourly_rate',
                'type' => 'decimal',
            ],
            [
                'name' => 'joining_date',
                'type' => 'date',
            ],
            [
                'name' => 'phone',
                'type' => 'text',
            ],
            [
                'name' => 'nid',
                'type' => 'text',
                'label' => 'NID',
            ],
            [
                'name' => 'is_operator',
                'type' => 'toggle',
                'filter' => true,
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
            'index' => ListEmployee::route('/'),
            'create' => CreateEmployee::route('/create'),
            'edit' => EditEmployee::route('/{record}/edit'),
        ];
    }
}

class ListEmployee extends ListRecords
{
    protected static string $resource = EmployeeResource::class;

    protected function getHeaderActions(): array
    {
        return [\Filament\Actions\CreateAction::make()];
    }
}

class CreateEmployee extends CreateRecord
{
    protected static string $resource = EmployeeResource::class;
}

class EditEmployee extends EditRecord
{
    protected static string $resource = EmployeeResource::class;
}
