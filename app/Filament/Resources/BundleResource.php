<?php

namespace App\Filament\Resources;

use App\Filament\Support\FieldBuilder;
use App\Filament\Support\GuardsModule;
use App\Models\Bundle;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BundleResource extends Resource
{
    use GuardsModule;

    protected static ?string $model = Bundle::class;

    protected static ?string $navigationIcon = 'heroicon-o-qr-code';

    protected static ?string $navigationGroup = 'Cutting';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'Bundle';

    public static function module(): string
    {
        return 'cutting';
    }

    public static function roles(): array
    {
        return [
    'cutting_master',
    'production_manager',
    'qc',
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
                'name' => 'cut_plan_id',
                'type' => 'relation',
                'relation' => 'cutPlan',
                'title' => 'lay_no',
                'required' => true,
                'filter' => true,
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
                'name' => 'bundle_no',
                'type' => 'text',
                'required' => true,
                'search' => true,
            ],
            [
                'name' => 'qr_code',
                'type' => 'text',
                'required' => true,
                'search' => true,
                'label' => 'QR / barcode',
            ],
            [
                'name' => 'color',
                'type' => 'text',
                'required' => true,
                'filter' => true,
            ],
            [
                'name' => 'size',
                'type' => 'select',
                'options' => 'sizes',
                'required' => true,
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
            [
                'name' => 'lot',
                'type' => 'text',
            ],
            [
                'name' => 'current_section',
                'type' => 'select',
                'options' => 'sections',
                'default' => 'cutting',
                'filter' => true,
                'badge' => true,
            ],
            [
                'name' => 'status',
                'type' => 'select',
                'options' => [
                    'open' => 'Open',
                    'in_process' => 'In process',
                    'completed' => 'Completed',
                    'hold' => 'Hold',
                ],
                'default' => 'open',
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
            'index' => ListBundle::route('/'),
            'create' => CreateBundle::route('/create'),
            'edit' => EditBundle::route('/{record}/edit'),
        ];
    }
}

class ListBundle extends ListRecords
{
    protected static string $resource = BundleResource::class;

    protected function getHeaderActions(): array
    {
        return [\Filament\Actions\CreateAction::make()];
    }
}

class CreateBundle extends CreateRecord
{
    protected static string $resource = BundleResource::class;
}

class EditBundle extends EditRecord
{
    protected static string $resource = BundleResource::class;
}
