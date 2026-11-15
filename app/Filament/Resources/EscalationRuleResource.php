<?php

namespace App\Filament\Resources;

use App\Filament\Support\FieldBuilder;
use App\Filament\Support\GuardsModule;
use App\Models\EscalationRule;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class EscalationRuleResource extends Resource
{
    use GuardsModule;

    protected static ?string $model = EscalationRule::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-up-circle';

    protected static ?string $navigationGroup = 'Issues';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'EscalationRule';

    public static function module(): string
    {
        return 'issues';
    }

    public static function roles(): array
    {
        return [
    'admin',
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
                'name' => 'issue_type',
                'type' => 'select',
                'options' => 'issue_types',
                'required' => true,
                'filter' => true,
            ],
            [
                'name' => 'severity',
                'type' => 'text',
            ],
            [
                'name' => 'escalate_after_hours',
                'type' => 'number',
                'default' => 8,
            ],
            [
                'name' => 'notify_role',
                'type' => 'select',
                'options' => 'roles',
                'required' => true,
            ],
            [
                'name' => 'auto_create_task',
                'type' => 'toggle',
                'default' => true,
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
            'index' => ListEscalationRule::route('/'),
            'create' => CreateEscalationRule::route('/create'),
            'edit' => EditEscalationRule::route('/{record}/edit'),
        ];
    }
}

class ListEscalationRule extends ListRecords
{
    protected static string $resource = EscalationRuleResource::class;

    protected function getHeaderActions(): array
    {
        return [\Filament\Actions\CreateAction::make()];
    }
}

class CreateEscalationRule extends CreateRecord
{
    protected static string $resource = EscalationRuleResource::class;
}

class EditEscalationRule extends EditRecord
{
    protected static string $resource = EscalationRuleResource::class;
}
