<?php

namespace App\Filament\Resources;

use App\Filament\Support\FieldBuilder;
use App\Filament\Support\GuardsModule;
use App\Models\LinePlan;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class LinePlanResource extends Resource
{
    use GuardsModule;

    protected static ?string $model = LinePlan::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    protected static ?string $navigationGroup = 'Sewing Floor';

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationLabel = 'LinePlan';

    public static function module(): string
    {
        return 'planning';
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
                'name' => 'plan_date',
                'type' => 'date',
                'required' => true,
            ],
            [
                'name' => 'planned_qty',
                'type' => 'number',
                'required' => true,
            ],
            [
                'name' => 'allocated_operators',
                'type' => 'number',
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
            'index' => ListLinePlan::route('/'),
            'create' => CreateLinePlan::route('/create'),
            'edit' => EditLinePlan::route('/{record}/edit'),
        ];
    }
}

class ListLinePlan extends ListRecords
{
    protected static string $resource = LinePlanResource::class;

    protected function getHeaderActions(): array
    {
        return [\Filament\Actions\CreateAction::make()];
    }
}

class CreateLinePlan extends CreateRecord
{
    protected static string $resource = LinePlanResource::class;
}

class EditLinePlan extends EditRecord
{
    protected static string $resource = LinePlanResource::class;
}
