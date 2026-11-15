<?php

namespace App\Filament\Resources;

use App\Filament\Support\FieldBuilder;
use App\Filament\Support\GuardsModule;
use App\Models\AuditLog;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AuditLogResource extends Resource
{
    use GuardsModule;

    protected static ?string $model = AuditLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?string $navigationGroup = 'Organization';

    protected static ?int $navigationSort = 9;

    protected static ?string $navigationLabel = 'AuditLog';

    public static function module(): string
    {
        return 'organization';
    }

    public static function roles(): array
    {
        return [
    'admin',
];
    }

    public static function allowsChanges(): bool
    {
        return false;
    }

    public static function fields(): array
    {
        return [
            [
                'name' => 'auditable_type',
                'type' => 'text',
                'search' => true,
            ],
            [
                'name' => 'auditable_id',
                'type' => 'number',
            ],
            [
                'name' => 'event',
                'type' => 'text',
                'filter' => true,
                'badge' => true,
            ],
            [
                'name' => 'ip',
                'type' => 'text',
            ],
            [
                'name' => 'created_at',
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
            ->actions([])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAuditLog::route('/'),
            'create' => CreateAuditLog::route('/create'),
            'edit' => EditAuditLog::route('/{record}/edit'),
        ];
    }
}

class ListAuditLog extends ListRecords
{
    protected static string $resource = AuditLogResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}

class CreateAuditLog extends CreateRecord
{
    protected static string $resource = AuditLogResource::class;
}

class EditAuditLog extends EditRecord
{
    protected static string $resource = AuditLogResource::class;
}
