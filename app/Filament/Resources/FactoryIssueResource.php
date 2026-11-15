<?php

namespace App\Filament\Resources;

use App\Filament\Support\FieldBuilder;
use App\Filament\Support\GuardsModule;
use App\Models\FactoryIssue;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class FactoryIssueResource extends Resource
{
    use GuardsModule;

    protected static ?string $model = FactoryIssue::class;

    protected static ?string $navigationIcon = 'heroicon-o-bell-alert';

    protected static ?string $navigationGroup = 'Issues';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Issue log';

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
                'name' => 'factory_id',
                'type' => 'relation',
                'relation' => 'factory',
                'title' => 'name',
                'required' => true,
                'filter' => true,
            ],
            [
                'name' => 'department',
                'type' => 'text',
            ],
            [
                'name' => 'type',
                'type' => 'select',
                'options' => 'issue_types',
                'required' => true,
                'filter' => true,
                'badge' => true,
            ],
            [
                'name' => 'severity',
                'type' => 'select',
                'options' => [
                    'low' => 'Low',
                    'medium' => 'Medium',
                    'high' => 'High',
                    'critical' => 'Critical',
                ],
                'default' => 'medium',
                'filter' => true,
                'badge' => true,
            ],
            [
                'name' => 'title',
                'type' => 'text',
                'required' => true,
                'search' => true,
            ],
            [
                'name' => 'status',
                'type' => 'select',
                'options' => 'issue_statuses',
                'default' => 'open',
                'filter' => true,
                'badge' => true,
            ],
            [
                'name' => 'fingerprint',
                'type' => 'text',
                'helper' => 'Leave blank for manual issues; detectors fill this.',
            ],
            [
                'name' => 'source',
                'type' => 'text',
                'default' => 'manual',
            ],
            [
                'name' => 'detail',
                'type' => 'textarea',
            ],
            [
                'name' => 'ai_recommendation',
                'type' => 'textarea',
                'label' => 'AI recommendation',
            ],
            [
                'name' => 'detected_at',
                'type' => 'datetime',
            ],
            [
                'name' => 'resolved_at',
                'type' => 'datetime',
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
            ->actions([
                Tables\Actions\Action::make('tasks')
                    ->label('Create tasks')
                    ->icon('heroicon-o-clipboard-document-check')
                    ->visible(fn ($record) => filled($record->suggested_actions))
                    ->action(function ($record) {
                        $record->openActions();
                        \Filament\Notifications\Notification::make()->title('Corrective tasks opened')->success()->send();
                    }),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([Tables\Actions\DeleteBulkAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFactoryIssue::route('/'),
            'create' => CreateFactoryIssue::route('/create'),
            'edit' => EditFactoryIssue::route('/{record}/edit'),
        ];
    }
}

class ListFactoryIssue extends ListRecords
{
    protected static string $resource = FactoryIssueResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('scan')
                ->label('Scan the floor')
                ->icon('heroicon-o-bolt')
                ->action(function () {
                    $count = app(\App\Services\Issues\IssueEngine::class)->scan();
                    \Filament\Notifications\Notification::make()->title($count.' new issues opened')->success()->send();
                }),
            \Filament\Actions\CreateAction::make(),
        ];
    }
}

class CreateFactoryIssue extends CreateRecord
{
    protected static string $resource = FactoryIssueResource::class;
}

class EditFactoryIssue extends EditRecord
{
    protected static string $resource = FactoryIssueResource::class;
}
