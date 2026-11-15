<?php

namespace App\Filament\Resources;

use App\Filament\Support\FieldBuilder;
use App\Filament\Support\GuardsModule;
use App\Models\TechPack;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TechPackResource extends Resource
{
    use GuardsModule;

    protected static ?string $model = TechPack::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Merchandising';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'TechPack';

    public static function module(): string
    {
        return 'styles';
    }

    public static function roles(): array
    {
        return [
    'merchandiser',
    'buyer',
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
                'name' => 'style_id',
                'type' => 'relation',
                'relation' => 'style',
                'title' => 'name',
                'required' => true,
                'filter' => true,
            ],
            [
                'name' => 'version',
                'type' => 'text',
                'required' => true,
            ],
            [
                'name' => 'status',
                'type' => 'select',
                'options' => 'approval',
                'default' => 'draft',
                'filter' => true,
                'badge' => true,
            ],
            [
                'name' => 'measurements',
                'type' => 'keyvalue',
                'label' => 'Measurements (point / cm)',
            ],
            [
                'name' => 'construction_notes',
                'type' => 'textarea',
            ],
            [
                'name' => 'artwork_notes',
                'type' => 'textarea',
            ],
            [
                'name' => 'approved_at',
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
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()])
            ->bulkActions([Tables\Actions\DeleteBulkAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTechPack::route('/'),
            'create' => CreateTechPack::route('/create'),
            'edit' => EditTechPack::route('/{record}/edit'),
        ];
    }
}

class ListTechPack extends ListRecords
{
    protected static string $resource = TechPackResource::class;

    protected function getHeaderActions(): array
    {
        return [\Filament\Actions\CreateAction::make()];
    }
}

class CreateTechPack extends CreateRecord
{
    protected static string $resource = TechPackResource::class;
}

class EditTechPack extends EditRecord
{
    protected static string $resource = TechPackResource::class;
}
