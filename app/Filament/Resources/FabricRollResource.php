<?php

namespace App\Filament\Resources;

use App\Filament\Support\FieldBuilder;
use App\Filament\Support\GuardsModule;
use App\Models\FabricRoll;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class FabricRollResource extends Resource
{
    use GuardsModule;

    protected static ?string $model = FabricRoll::class;

    protected static ?string $navigationIcon = 'heroicon-o-circle-stack';

    protected static ?string $navigationGroup = 'Materials';

    protected static ?int $navigationSort = 7;

    protected static ?string $navigationLabel = 'FabricRoll';

    public static function module(): string
    {
        return 'inventory';
    }

    public static function roles(): array
    {
        return [
    'store',
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
                'name' => 'factory_id',
                'type' => 'relation',
                'relation' => 'factory',
                'title' => 'name',
                'required' => true,
            ],
            [
                'name' => 'fabric_id',
                'type' => 'relation',
                'relation' => 'fabric',
                'title' => 'name',
                'required' => true,
                'filter' => true,
            ],
            [
                'name' => 'buyer_order_id',
                'type' => 'relation',
                'relation' => 'buyerOrder',
                'title' => 'po_number',
                'filter' => true,
            ],
            [
                'name' => 'roll_no',
                'type' => 'text',
                'required' => true,
                'search' => true,
            ],
            [
                'name' => 'lot',
                'type' => 'text',
                'search' => true,
            ],
            [
                'name' => 'shade',
                'type' => 'text',
                'search' => true,
                'filter' => true,
            ],
            [
                'name' => 'width_inch',
                'type' => 'decimal',
            ],
            [
                'name' => 'gsm',
                'type' => 'number',
            ],
            [
                'name' => 'length_m',
                'type' => 'decimal',
                'required' => true,
            ],
            [
                'name' => 'remaining_m',
                'type' => 'decimal',
                'required' => true,
            ],
            [
                'name' => 'location',
                'type' => 'text',
            ],
            [
                'name' => 'status',
                'type' => 'select',
                'options' => [
                    'available' => 'Available',
                    'reserved' => 'Reserved',
                    'issued' => 'Issued',
                    'exhausted' => 'Exhausted',
                    'rejected' => 'Rejected',
                ],
                'default' => 'available',
                'filter' => true,
                'badge' => true,
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
            'index' => ListFabricRoll::route('/'),
            'create' => CreateFabricRoll::route('/create'),
            'edit' => EditFabricRoll::route('/{record}/edit'),
        ];
    }
}

class ListFabricRoll extends ListRecords
{
    protected static string $resource = FabricRollResource::class;

    protected function getHeaderActions(): array
    {
        return [\Filament\Actions\CreateAction::make()];
    }
}

class CreateFabricRoll extends CreateRecord
{
    protected static string $resource = FabricRollResource::class;
}

class EditFabricRoll extends EditRecord
{
    protected static string $resource = FabricRollResource::class;
}
