<?php

namespace App\Filament\Resources;

use App\Filament\Support\FieldBuilder;
use App\Filament\Support\GuardsModule;
use App\Models\IssueAction;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class IssueActionResource extends Resource
{
    use GuardsModule;

    protected static ?string $model = IssueAction::class;

    protected static ?string $navigationIcon = 'heroicon-o-check-circle';

    protected static ?string $navigationGroup = 'Issues';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'IssueAction';

    public static function module(): string
    {
        return 'issues';
    }

    public static function roles(): array
    {
        return [
    'merchandiser',
    'production_manager',
    'cutting_master',
    'qc',
    'store',
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
                'name' => 'factory_issue_id',
                'type' => 'relation',
                'relation' => 'factoryIssue',
                'title' => 'title',
                'required' => true,
                'filter' => true,
            ],
            [
                'name' => 'title',
                'type' => 'text',
                'required' => true,
                'search' => true,
            ],
            [
                'name' => 'owner_role',
                'type' => 'select',
                'options' => 'roles',
                'filter' => true,
            ],
            [
                'name' => 'status',
                'type' => 'select',
                'options' => [
                    'open' => 'Open',
                    'done' => 'Done',
                ],
                'default' => 'open',
                'filter' => true,
                'badge' => true,
            ],
            [
                'name' => 'due_on',
                'type' => 'date',
            ],
            [
                'name' => 'completed_at',
                'type' => 'datetime',
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
            'index' => ListIssueAction::route('/'),
            'create' => CreateIssueAction::route('/create'),
            'edit' => EditIssueAction::route('/{record}/edit'),
        ];
    }
}

class ListIssueAction extends ListRecords
{
    protected static string $resource = IssueActionResource::class;

    protected function getHeaderActions(): array
    {
        return [\Filament\Actions\CreateAction::make()];
    }
}

class CreateIssueAction extends CreateRecord
{
    protected static string $resource = IssueActionResource::class;
}

class EditIssueAction extends EditRecord
{
    protected static string $resource = IssueActionResource::class;
}
