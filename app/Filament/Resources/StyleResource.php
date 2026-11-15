<?php

namespace App\Filament\Resources;

use App\Filament\Support\FieldBuilder;
use App\Filament\Support\GuardsModule;
use App\Models\Style;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class StyleResource extends Resource
{
    use GuardsModule;

    protected static ?string $model = Style::class;

    protected static ?string $navigationIcon = 'heroicon-o-swatch';

    protected static ?string $navigationGroup = 'Merchandising';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Style';

    public static function module(): string
    {
        return 'styles';
    }

    public static function roles(): array
    {
        return [
    'merchandiser',
    'buyer',
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
                'name' => 'company_id',
                'type' => 'relation',
                'relation' => 'company',
                'title' => 'name',
                'required' => true,
            ],
            [
                'name' => 'buyer_id',
                'type' => 'relation',
                'relation' => 'buyer',
                'title' => 'name',
                'required' => true,
                'filter' => true,
            ],
            [
                'name' => 'style_no',
                'type' => 'text',
                'required' => true,
                'search' => true,
                'label' => 'Style no',
            ],
            [
                'name' => 'name',
                'type' => 'text',
                'required' => true,
                'search' => true,
            ],
            [
                'name' => 'season',
                'type' => 'text',
            ],
            [
                'name' => 'category',
                'type' => 'select',
                'options' => 'style_categories',
                'required' => true,
                'filter' => true,
                'badge' => true,
            ],
            [
                'name' => 'fit',
                'type' => 'select',
                'options' => 'fits',
                'filter' => true,
            ],
            [
                'name' => 'fabric_type',
                'type' => 'text',
                'search' => true,
                'label' => 'Fabric',
            ],
            [
                'name' => 'wash',
                'type' => 'text',
                'filter' => true,
            ],
            [
                'name' => 'gsm',
                'type' => 'number',
            ],
            [
                'name' => 'composition',
                'type' => 'text',
            ],
            [
                'name' => 'smv',
                'type' => 'decimal',
                'required' => true,
            ],
            [
                'name' => 'status',
                'type' => 'select',
                'options' => [
                    'active' => 'Active',
                    'development' => 'Development',
                    'dropped' => 'Dropped',
                ],
                'default' => 'active',
                'filter' => true,
                'badge' => true,
            ],
            [
                'name' => 'description',
                'type' => 'textarea',
            ],
            [
                'name' => 'image_path',
                'type' => 'file',
                'label' => 'Image',
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
            ->actions([
                Tables\Actions\Action::make('listing')
                    ->label('AI listing')
                    ->icon('heroicon-o-sparkles')
                    ->form([
                        \Filament\Forms\Components\Select::make('locale')->options(['en' => 'English', 'bn' => 'Bengali'])->default('en')->required(),
                    ])
                    ->action(function ($record, array $data) {
                        $listing = app(\App\Services\Catalog\StyleListingService::class)->generate($record, $data['locale']);
                        \Filament\Notifications\Notification::make()
                            ->title('Listing stored')
                            ->body(\Illuminate\Support\Str::limit((string) ($listing['summary'] ?? $listing['title'] ?? 'Draft saved on the style.'), 180))
                            ->success()
                            ->send();
                    }),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([Tables\Actions\DeleteBulkAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStyle::route('/'),
            'create' => CreateStyle::route('/create'),
            'edit' => EditStyle::route('/{record}/edit'),
        ];
    }
}

class ListStyle extends ListRecords
{
    protected static string $resource = StyleResource::class;

    protected function getHeaderActions(): array
    {
        return [\Filament\Actions\CreateAction::make()];
    }
}

class CreateStyle extends CreateRecord
{
    protected static string $resource = StyleResource::class;
}

class EditStyle extends EditRecord
{
    protected static string $resource = StyleResource::class;
}
