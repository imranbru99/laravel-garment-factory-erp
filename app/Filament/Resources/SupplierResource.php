<?php

namespace App\Filament\Resources;

use App\Filament\Support\FieldBuilder;
use App\Filament\Support\GuardsModule;
use App\Models\Supplier;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SupplierResource extends Resource
{
    use GuardsModule;

    protected static ?string $model = Supplier::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';

    protected static ?string $navigationGroup = 'Materials';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Supplier';

    public static function module(): string
    {
        return 'procurement';
    }

    public static function roles(): array
    {
        return [
    'store',
    'merchandiser',
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
                'name' => 'company_id',
                'type' => 'relation',
                'relation' => 'company',
                'title' => 'name',
                'required' => true,
            ],
            [
                'name' => 'name',
                'type' => 'text',
                'required' => true,
                'search' => true,
            ],
            [
                'name' => 'type',
                'type' => 'select',
                'options' => [
                    'fabric' => 'Fabric',
                    'trim' => 'Trim',
                    'both' => 'Both',
                ],
                'default' => 'both',
                'filter' => true,
            ],
            [
                'name' => 'contact',
                'type' => 'text',
            ],
            [
                'name' => 'phone',
                'type' => 'text',
            ],
            [
                'name' => 'email',
                'type' => 'text',
            ],
            [
                'name' => 'lead_time_days',
                'type' => 'number',
                'default' => 14,
            ],
            [
                'name' => 'rating',
                'type' => 'number',
            ],
            [
                'name' => 'address',
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
            'index' => ListSupplier::route('/'),
            'create' => CreateSupplier::route('/create'),
            'edit' => EditSupplier::route('/{record}/edit'),
        ];
    }
}

class ListSupplier extends ListRecords
{
    protected static string $resource = SupplierResource::class;

    protected function getHeaderActions(): array
    {
        return [\Filament\Actions\CreateAction::make()];
    }
}

class CreateSupplier extends CreateRecord
{
    protected static string $resource = SupplierResource::class;
}

class EditSupplier extends EditRecord
{
    protected static string $resource = SupplierResource::class;
}
