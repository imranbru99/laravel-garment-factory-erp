<?php

namespace App\Filament\Resources;

use App\Filament\Support\FieldBuilder;
use App\Filament\Support\GuardsModule;
use App\Models\SampleComment;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SampleCommentResource extends Resource
{
    use GuardsModule;

    protected static ?string $model = SampleComment::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationGroup = 'Merchandising';

    protected static ?int $navigationSort = 6;

    protected static ?string $navigationLabel = 'SampleComment';

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
                'name' => 'sample_id',
                'type' => 'relation',
                'relation' => 'sample',
                'title' => 'stage',
                'required' => true,
                'filter' => true,
            ],
            [
                'name' => 'author_name',
                'type' => 'text',
                'required' => true,
                'search' => true,
            ],
            [
                'name' => 'is_buyer',
                'type' => 'toggle',
                'label' => 'Buyer comment',
            ],
            [
                'name' => 'body',
                'type' => 'textarea',
                'required' => true,
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
            'index' => ListSampleComment::route('/'),
            'create' => CreateSampleComment::route('/create'),
            'edit' => EditSampleComment::route('/{record}/edit'),
        ];
    }
}

class ListSampleComment extends ListRecords
{
    protected static string $resource = SampleCommentResource::class;

    protected function getHeaderActions(): array
    {
        return [\Filament\Actions\CreateAction::make()];
    }
}

class CreateSampleComment extends CreateRecord
{
    protected static string $resource = SampleCommentResource::class;
}

class EditSampleComment extends EditRecord
{
    protected static string $resource = SampleCommentResource::class;
}
