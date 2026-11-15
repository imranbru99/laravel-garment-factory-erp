<?php

namespace App\Filament\Resources;

use App\Filament\Support\FieldBuilder;
use App\Filament\Support\GuardsModule;
use App\Models\BundleComponent;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BundleComponentResource extends Resource
{
    use GuardsModule;

    protected static ?string $model = BundleComponent::class;

    protected static ?string $navigationIcon = 'heroicon-o-puzzle-piece';

    protected static ?string $navigationGroup = 'Cutting';

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationLabel = 'BundleComponent';

    public static function module(): string
    {
        return 'cutting';
    }

    public static function roles(): array
    {
        return [
    'cutting_master',
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
                'name' => 'bundle_id',
                'type' => 'relation',
                'relation' => 'bundle',
                'title' => 'bundle_no',
                'required' => true,
                'filter' => true,
            ],
            [
                'name' => 'component',
                'type' => 'select',
                'options' => 'components',
                'required' => true,
                'filter' => true,
            ],
            [
                'name' => 'quantity',
                'type' => 'number',
                'required' => true,
            ],
            [
                'name' => 'shade',
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
            'index' => ListBundleComponent::route('/'),
            'create' => CreateBundleComponent::route('/create'),
            'edit' => EditBundleComponent::route('/{record}/edit'),
        ];
    }
}

class ListBundleComponent extends ListRecords
{
    protected static string $resource = BundleComponentResource::class;

    protected function getHeaderActions(): array
    {
        return [\Filament\Actions\CreateAction::make()];
    }
}

class CreateBundleComponent extends CreateRecord
{
    protected static string $resource = BundleComponentResource::class;
}

class EditBundleComponent extends EditRecord
{
    protected static string $resource = BundleComponentResource::class;
}
