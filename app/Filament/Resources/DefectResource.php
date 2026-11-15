<?php

namespace App\Filament\Resources;

use App\Filament\Support\FieldBuilder;
use App\Filament\Support\GuardsModule;
use App\Models\Defect;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DefectResource extends Resource
{
    use GuardsModule;

    protected static ?string $model = Defect::class;

    protected static ?string $navigationIcon = 'heroicon-o-exclamation-triangle';

    protected static ?string $navigationGroup = 'Quality';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Defect';

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
                'name' => 'inspection_id',
                'type' => 'relation',
                'relation' => 'inspection',
                'title' => 'type',
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
                'name' => 'code',
                'type' => 'text',
                'required' => true,
                'search' => true,
            ],
            [
                'name' => 'name',
                'type' => 'text',
                'required' => true,
                'search' => true,
            ],
            [
                'name' => 'category',
                'type' => 'select',
                'options' => 'defect_categories',
                'required' => true,
                'filter' => true,
                'badge' => true,
            ],
            [
                'name' => 'quantity',
                'type' => 'number',
                'default' => 1,
            ],
            [
                'name' => 'severity',
                'type' => 'select',
                'options' => [
                    'minor' => 'Minor',
                    'major' => 'Major',
                    'critical' => 'Critical',
                ],
                'default' => 'major',
                'filter' => true,
                'badge' => true,
            ],
            [
                'name' => 'photo_path',
                'type' => 'file',
                'label' => 'Photo',
            ],
            [
                'name' => 'ai_category',
                'type' => 'text',
            ],
            [
                'name' => 'root_cause',
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
            'index' => ListDefect::route('/'),
            'create' => CreateDefect::route('/create'),
            'edit' => EditDefect::route('/{record}/edit'),
        ];
    }
}

class ListDefect extends ListRecords
{
    protected static string $resource = DefectResource::class;

    protected function getHeaderActions(): array
    {
        return [\Filament\Actions\CreateAction::make()];
    }
}

class CreateDefect extends CreateRecord
{
    protected static string $resource = DefectResource::class;
}

class EditDefect extends EditRecord
{
    protected static string $resource = DefectResource::class;
}
