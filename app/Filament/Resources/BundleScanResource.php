<?php

namespace App\Filament\Resources;

use App\Filament\Support\FieldBuilder;
use App\Filament\Support\GuardsModule;
use App\Models\BundleScan;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BundleScanResource extends Resource
{
    use GuardsModule;

    protected static ?string $model = BundleScan::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-path';

    protected static ?string $navigationGroup = 'Sewing Floor';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'BundleScan';

    public static function module(): string
    {
        return 'sewing';
    }

    public static function roles(): array
    {
        return [
    'production_manager',
    'cutting_master',
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
                'name' => 'bundle_id',
                'type' => 'relation',
                'relation' => 'bundle',
                'title' => 'bundle_no',
                'required' => true,
                'filter' => true,
            ],
            [
                'name' => 'operation_id',
                'type' => 'relation',
                'relation' => 'operation',
                'title' => 'name',
            ],
            [
                'name' => 'sewing_line_id',
                'type' => 'relation',
                'relation' => 'sewingLine',
                'title' => 'code',
                'filter' => true,
            ],
            [
                'name' => 'employee_id',
                'type' => 'relation',
                'relation' => 'employee',
                'title' => 'name',
            ],
            [
                'name' => 'direction',
                'type' => 'select',
                'options' => [
                    'in' => 'In',
                    'out' => 'Out',
                ],
                'required' => true,
                'filter' => true,
                'badge' => true,
            ],
            [
                'name' => 'section',
                'type' => 'select',
                'options' => 'sections',
                'required' => true,
                'filter' => true,
            ],
            [
                'name' => 'scanned_at',
                'type' => 'datetime',
                'required' => true,
            ],
            [
                'name' => 'quantity',
                'type' => 'number',
                'required' => true,
            ],
            [
                'name' => 'note',
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
            'index' => ListBundleScan::route('/'),
            'create' => CreateBundleScan::route('/create'),
            'edit' => EditBundleScan::route('/{record}/edit'),
        ];
    }
}

class ListBundleScan extends ListRecords
{
    protected static string $resource = BundleScanResource::class;

    protected function getHeaderActions(): array
    {
        return [\Filament\Actions\CreateAction::make()];
    }
}

class CreateBundleScan extends CreateRecord
{
    protected static string $resource = BundleScanResource::class;
}

class EditBundleScan extends EditRecord
{
    protected static string $resource = BundleScanResource::class;
}
