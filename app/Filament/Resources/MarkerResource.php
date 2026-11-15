<?php

namespace App\Filament\Resources;

use App\Filament\Support\FieldBuilder;
use App\Filament\Support\GuardsModule;
use App\Models\Marker;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class MarkerResource extends Resource
{
    use GuardsModule;

    protected static ?string $model = Marker::class;

    protected static ?string $navigationIcon = 'heroicon-o-map';

    protected static ?string $navigationGroup = 'Cutting';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Marker';

    public static function module(): string
    {
        return 'cutting';
    }

    public static function roles(): array
    {
        return [
    'cutting_master',
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
                'name' => 'buyer_order_id',
                'type' => 'relation',
                'relation' => 'buyerOrder',
                'title' => 'po_number',
                'required' => true,
                'filter' => true,
            ],
            [
                'name' => 'style_id',
                'type' => 'relation',
                'relation' => 'style',
                'title' => 'name',
                'required' => true,
            ],
            [
                'name' => 'fabric_id',
                'type' => 'relation',
                'relation' => 'fabric',
                'title' => 'name',
            ],
            [
                'name' => 'name',
                'type' => 'text',
                'required' => true,
                'search' => true,
            ],
            [
                'name' => 'width_inch',
                'type' => 'decimal',
            ],
            [
                'name' => 'efficiency',
                'type' => 'decimal',
                'label' => 'Marker efficiency %',
            ],
            [
                'name' => 'planned_plies',
                'type' => 'number',
            ],
            [
                'name' => 'status',
                'type' => 'select',
                'options' => [
                    'draft' => 'Draft',
                    'approved' => 'Approved',
                    'used' => 'Used',
                ],
                'default' => 'draft',
                'filter' => true,
                'badge' => true,
            ],
            [
                'name' => 'sizes',
                'type' => 'keyvalue',
                'label' => 'Size ratio',
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
            'index' => ListMarker::route('/'),
            'create' => CreateMarker::route('/create'),
            'edit' => EditMarker::route('/{record}/edit'),
        ];
    }
}

class ListMarker extends ListRecords
{
    protected static string $resource = MarkerResource::class;

    protected function getHeaderActions(): array
    {
        return [\Filament\Actions\CreateAction::make()];
    }
}

class CreateMarker extends CreateRecord
{
    protected static string $resource = MarkerResource::class;
}

class EditMarker extends EditRecord
{
    protected static string $resource = MarkerResource::class;
}
