<?php

namespace App\Filament\Resources;

use App\Filament\Support\FieldBuilder;
use App\Filament\Support\GuardsModule;
use App\Models\ProductionLog;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProductionLogResource extends Resource
{
    use GuardsModule;

    protected static ?string $model = ProductionLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationGroup = 'Sewing Floor';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'ProductionLog';

    public static function module(): string
    {
        return 'sewing';
    }

    public static function roles(): array
    {
        return [
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
                'name' => 'sewing_line_id',
                'type' => 'relation',
                'relation' => 'sewingLine',
                'title' => 'code',
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
                'name' => 'employee_id',
                'type' => 'relation',
                'relation' => 'employee',
                'title' => 'name',
            ],
            [
                'name' => 'log_date',
                'type' => 'date',
                'required' => true,
            ],
            [
                'name' => 'hour_slot',
                'type' => 'number',
            ],
            [
                'name' => 'section',
                'type' => 'select',
                'options' => 'sections',
                'required' => true,
                'filter' => true,
            ],
            [
                'name' => 'target_qty',
                'type' => 'number',
            ],
            [
                'name' => 'actual_qty',
                'type' => 'number',
            ],
            [
                'name' => 'smv',
                'type' => 'decimal',
            ],
            [
                'name' => 'efficiency',
                'type' => 'decimal',
            ],
            [
                'name' => 'defect_qty',
                'type' => 'number',
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
            'index' => ListProductionLog::route('/'),
            'create' => CreateProductionLog::route('/create'),
            'edit' => EditProductionLog::route('/{record}/edit'),
        ];
    }
}

class ListProductionLog extends ListRecords
{
    protected static string $resource = ProductionLogResource::class;

    protected function getHeaderActions(): array
    {
        return [\Filament\Actions\CreateAction::make()];
    }
}

class CreateProductionLog extends CreateRecord
{
    protected static string $resource = ProductionLogResource::class;
}

class EditProductionLog extends EditRecord
{
    protected static string $resource = ProductionLogResource::class;
}
