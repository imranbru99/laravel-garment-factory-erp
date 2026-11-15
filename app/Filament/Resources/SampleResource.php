<?php

namespace App\Filament\Resources;

use App\Filament\Support\FieldBuilder;
use App\Filament\Support\GuardsModule;
use App\Models\Sample;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SampleResource extends Resource
{
    use GuardsModule;

    protected static ?string $model = Sample::class;

    protected static ?string $navigationIcon = 'heroicon-o-beaker';

    protected static ?string $navigationGroup = 'Merchandising';

    protected static ?int $navigationSort = 5;

    protected static ?string $navigationLabel = 'Sample';

    public static function module(): string
    {
        return 'styles';
    }

    public static function roles(): array
    {
        return [
    'merchandiser',
    'buyer',
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
                'name' => 'style_id',
                'type' => 'relation',
                'relation' => 'style',
                'title' => 'name',
                'required' => true,
                'filter' => true,
            ],
            [
                'name' => 'buyer_order_id',
                'type' => 'relation',
                'relation' => 'buyerOrder',
                'title' => 'po_number',
            ],
            [
                'name' => 'stage',
                'type' => 'select',
                'options' => 'sample_stages',
                'required' => true,
                'filter' => true,
                'badge' => true,
            ],
            [
                'name' => 'status',
                'type' => 'select',
                'options' => 'approval',
                'default' => 'pending',
                'filter' => true,
                'badge' => true,
            ],
            [
                'name' => 'version',
                'type' => 'text',
                'default' => '1',
            ],
            [
                'name' => 'requested_on',
                'type' => 'date',
            ],
            [
                'name' => 'due_on',
                'type' => 'date',
            ],
            [
                'name' => 'comments_summary',
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
            'index' => ListSample::route('/'),
            'create' => CreateSample::route('/create'),
            'edit' => EditSample::route('/{record}/edit'),
        ];
    }
}

class ListSample extends ListRecords
{
    protected static string $resource = SampleResource::class;

    protected function getHeaderActions(): array
    {
        return [\Filament\Actions\CreateAction::make()];
    }
}

class CreateSample extends CreateRecord
{
    protected static string $resource = SampleResource::class;
}

class EditSample extends EditRecord
{
    protected static string $resource = SampleResource::class;
}
