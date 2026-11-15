<?php

namespace App\Filament\Resources;

use App\Filament\Support\FieldBuilder;
use App\Filament\Support\GuardsModule;
use App\Models\WorkerAttendance;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class WorkerAttendanceResource extends Resource
{
    use GuardsModule;

    protected static ?string $model = WorkerAttendance::class;

    protected static ?string $navigationIcon = 'heroicon-o-finger-print';

    protected static ?string $navigationGroup = 'People';

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationLabel = 'Attendance';

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
                'name' => 'punched_at',
                'type' => 'datetime',
                'required' => true,
            ],
            [
                'name' => 'direction',
                'type' => 'select',
                'options' => [
                    'in' => 'In',
                    'out' => 'Out',
                ],
                'required' => true,
                'filter' => true,
                'badge' => true,
            ],
            [
                'name' => 'source',
                'type' => 'select',
                'options' => [
                    'manual' => 'Manual',
                    'biometric' => 'Biometric',
                    'gps' => 'GPS',
                    'qr' => 'QR',
                ],
                'default' => 'manual',
                'filter' => true,
            ],
            [
                'name' => 'latitude',
                'type' => 'decimal',
            ],
            [
                'name' => 'longitude',
                'type' => 'decimal',
            ],
            [
                'name' => 'device_name',
                'type' => 'text',
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
            'index' => ListWorkerAttendance::route('/'),
            'create' => CreateWorkerAttendance::route('/create'),
            'edit' => EditWorkerAttendance::route('/{record}/edit'),
        ];
    }
}

class ListWorkerAttendance extends ListRecords
{
    protected static string $resource = WorkerAttendanceResource::class;

    protected function getHeaderActions(): array
    {
        return [\Filament\Actions\CreateAction::make()];
    }
}

class CreateWorkerAttendance extends CreateRecord
{
    protected static string $resource = WorkerAttendanceResource::class;
}

class EditWorkerAttendance extends EditRecord
{
    protected static string $resource = WorkerAttendanceResource::class;
}
