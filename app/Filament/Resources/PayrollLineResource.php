<?php

namespace App\Filament\Resources;

use App\Filament\Support\FieldBuilder;
use App\Filament\Support\GuardsModule;
use App\Models\PayrollLine;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PayrollLineResource extends Resource
{
    use GuardsModule;

    protected static ?string $model = PayrollLine::class;

    protected static ?string $navigationIcon = 'heroicon-o-receipt-percent';

    protected static ?string $navigationGroup = 'People';

    protected static ?int $navigationSort = 6;

    protected static ?string $navigationLabel = 'PayrollLine';

    public static function module(): string
    {
        return 'hr';
    }

    public static function roles(): array
    {
        return [
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
                'name' => 'payroll_period_id',
                'type' => 'relation',
                'relation' => 'payrollPeriod',
                'title' => 'name',
                'required' => true,
                'filter' => true,
            ],
            [
                'name' => 'employee_id',
                'type' => 'relation',
                'relation' => 'employee',
                'title' => 'name',
                'required' => true,
                'filter' => true,
            ],
            [
                'name' => 'present_days',
                'type' => 'decimal',
            ],
            [
                'name' => 'overtime_hours',
                'type' => 'decimal',
            ],
            [
                'name' => 'pieces',
                'type' => 'number',
            ],
            [
                'name' => 'piece_amount',
                'type' => 'decimal',
            ],
            [
                'name' => 'attendance_amount',
                'type' => 'decimal',
            ],
            [
                'name' => 'gross',
                'type' => 'decimal',
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
            'index' => ListPayrollLine::route('/'),
            'create' => CreatePayrollLine::route('/create'),
            'edit' => EditPayrollLine::route('/{record}/edit'),
        ];
    }
}

class ListPayrollLine extends ListRecords
{
    protected static string $resource = PayrollLineResource::class;

    protected function getHeaderActions(): array
    {
        return [\Filament\Actions\CreateAction::make()];
    }
}

class CreatePayrollLine extends CreateRecord
{
    protected static string $resource = PayrollLineResource::class;
}

class EditPayrollLine extends EditRecord
{
    protected static string $resource = PayrollLineResource::class;
}
