<?php

namespace App\Filament\Resources;

use App\Filament\Support\FieldBuilder;
use App\Filament\Support\GuardsModule;
use App\Models\Inspection;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class InspectionResource extends Resource
{
    use GuardsModule;

    protected static ?string $model = Inspection::class;

    protected static ?string $navigationIcon = 'heroicon-o-check-badge';

    protected static ?string $navigationGroup = 'Quality';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Inspection';

    public static function module(): string
    {
        return 'quality';
    }

    public static function roles(): array
    {
        return [
    'qc',
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
                'name' => 'bundle_id',
                'type' => 'relation',
                'relation' => 'bundle',
                'title' => 'bundle_no',
            ],
            [
                'name' => 'type',
                'type' => 'select',
                'options' => 'inspection_types',
                'required' => true,
                'filter' => true,
                'badge' => true,
            ],
            [
                'name' => 'inspected_qty',
                'type' => 'number',
                'required' => true,
            ],
            [
                'name' => 'passed_qty',
                'type' => 'number',
            ],
            [
                'name' => 'rejected_qty',
                'type' => 'number',
            ],
            [
                'name' => 'aql_level',
                'type' => 'text',
                'label' => 'AQL',
            ],
            [
                'name' => 'result',
                'type' => 'select',
                'options' => [
                    'pass' => 'Pass',
                    'fail' => 'Fail',
                    'hold' => 'Hold',
                ],
                'default' => 'hold',
                'filter' => true,
                'badge' => true,
            ],
            [
                'name' => 'inspected_at',
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
            'index' => ListInspection::route('/'),
            'create' => CreateInspection::route('/create'),
            'edit' => EditInspection::route('/{record}/edit'),
        ];
    }
}

class ListInspection extends ListRecords
{
    protected static string $resource = InspectionResource::class;

    protected function getHeaderActions(): array
    {
        return [\Filament\Actions\CreateAction::make()];
    }
}

class CreateInspection extends CreateRecord
{
    protected static string $resource = InspectionResource::class;
}

class EditInspection extends EditRecord
{
    protected static string $resource = InspectionResource::class;
}
