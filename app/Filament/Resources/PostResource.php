<?php

namespace App\Filament\Resources;

use App\Filament\Support\FieldBuilder;
use App\Filament\Support\GuardsModule;
use App\Models\Post;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PostResource extends Resource
{
    use GuardsModule;

    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-newspaper';

    protected static ?string $navigationGroup = 'Website';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Post';

    public static function module(): string
    {
        return 'website';
    }

    public static function roles(): array
    {
        return [
    'merchandiser',
    'admin',
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
                'name' => 'title',
                'type' => 'text',
                'required' => true,
                'search' => true,
            ],
            [
                'name' => 'locale',
                'type' => 'select',
                'options' => [
                    'en' => 'English',
                    'bn' => 'Bengali',
                ],
                'default' => 'en',
                'filter' => true,
            ],
            [
                'name' => 'excerpt',
                'type' => 'textarea',
            ],
            [
                'name' => 'body',
                'type' => 'textarea',
                'required' => true,
            ],
            [
                'name' => 'published_at',
                'type' => 'datetime',
            ],
            [
                'name' => 'is_published',
                'type' => 'toggle',
                'filter' => true,
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
            'index' => ListPost::route('/'),
            'create' => CreatePost::route('/create'),
            'edit' => EditPost::route('/{record}/edit'),
        ];
    }
}

class ListPost extends ListRecords
{
    protected static string $resource = PostResource::class;

    protected function getHeaderActions(): array
    {
        return [\Filament\Actions\CreateAction::make()];
    }
}

class CreatePost extends CreateRecord
{
    protected static string $resource = PostResource::class;
}

class EditPost extends EditRecord
{
    protected static string $resource = PostResource::class;
}
