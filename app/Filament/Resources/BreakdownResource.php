<?php

namespace App\Filament\Resources;

use App\Filament\Support\FieldBuilder;
use App\Filament\Support\GuardsModule;
use App\Models\Breakdown;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BreakdownResource extends Resource
{
    use GuardsModule;

    protected static ?string $model = Breakdown::class;

    protected static ?string $navigationIcon = 'heroicon-o-bolt';

    protected static ?string $navigationGroup = 'Maintenance';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Breakdown';

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
                'name' => 'machine_id',
                'type' => 'relation',
                'relation' => 'machine',
                'title' => 'code',
                'required' => true,
                'filter' => true,
            ],
            [
                'name' => 'factory_id',
                'type' => 'relation',
                'relation' => 'factory',
                'title' => 'name',
                'required' => true,
            ],
            [
                'name' => 'reported_at',
                'type' => 'datetime',
                'required' => true,
            ],
            [
                'name' => 'resolved_at',
                'type' => 'datetime',
            ],
            [
                'name' => 'minutes_down',
                'type' => 'number',
            ],
            [
                'name' => 'status',
                'type' => 'select',
                'options' => [
                    'open' => 'Open',
                    'resolved' => 'Resolved',
                ],
                'default' => 'open',
                'filter' => true,
                'badge' => true,
            ],
            [
                'name' => 'reason',
                'type' => 'textarea',
            ],
            [
                'name' => 'action_taken',
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
            'index' => ListBreakdown::route('/'),
            'create' => CreateBreakdown::route('/create'),
            'edit' => EditBreakdown::route('/{record}/edit'),
        ];
    }
}

class ListBreakdown extends ListRecords
{
    protected static string $resource = BreakdownResource::class;

    protected function getHeaderActions(): array
    {
        return [\Filament\Actions\CreateAction::make()];
    }
}

class CreateBreakdown extends CreateRecord
{
    protected static string $resource = BreakdownResource::class;
}

class EditBreakdown extends EditRecord
{
    protected static string $resource = BreakdownResource::class;
}
