<?php

namespace App\Filament\Resources;

use App\Filament\Support\FieldBuilder;
use App\Filament\Support\GuardsModule;
use App\Models\LeaveRequest;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class LeaveRequestResource extends Resource
{
    use GuardsModule;

    protected static ?string $model = LeaveRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-date-range';

    protected static ?string $navigationGroup = 'People';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'LeaveRequest';

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
                'name' => 'employee_id',
                'type' => 'relation',
                'relation' => 'employee',
                'title' => 'name',
                'required' => true,
                'filter' => true,
            ],
            [
                'name' => 'type',
                'type' => 'select',
                'options' => [
                    'casual' => 'Casual',
                    'sick' => 'Sick',
                    'earned' => 'Earned',
                    'unpaid' => 'Unpaid',
                ],
                'required' => true,
            ],
            [
                'name' => 'start_date',
                'type' => 'date',
                'required' => true,
            ],
            [
                'name' => 'end_date',
                'type' => 'date',
                'required' => true,
            ],
            [
                'name' => 'days',
                'type' => 'decimal',
                'required' => true,
            ],
            [
                'name' => 'status',
                'type' => 'select',
                'options' => [
                    'pending' => 'Pending',
                    'approved' => 'Approved',
                    'rejected' => 'Rejected',
                ],
                'default' => 'pending',
                'filter' => true,
                'badge' => true,
            ],
            [
                'name' => 'reason',
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
            'index' => ListLeaveRequest::route('/'),
            'create' => CreateLeaveRequest::route('/create'),
            'edit' => EditLeaveRequest::route('/{record}/edit'),
        ];
    }
}

class ListLeaveRequest extends ListRecords
{
    protected static string $resource = LeaveRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [\Filament\Actions\CreateAction::make()];
    }
}

class CreateLeaveRequest extends CreateRecord
{
    protected static string $resource = LeaveRequestResource::class;
}

class EditLeaveRequest extends EditRecord
{
    protected static string $resource = LeaveRequestResource::class;
}
