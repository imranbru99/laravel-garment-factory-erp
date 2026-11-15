<?php

namespace App\Filament\Resources;

use App\Filament\Support\FieldBuilder;
use App\Filament\Support\GuardsModule;
use App\Models\Buyer;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BuyerResource extends Resource
{
    use GuardsModule;

    protected static ?string $model = Buyer::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'Merchandising';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Buyer';

    public static function module(): string
    {
        return 'orders';
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
                'name' => 'company_id',
                'type' => 'relation',
                'relation' => 'company',
                'title' => 'name',
                'required' => true,
                'filter' => true,
            ],
            [
                'name' => 'name',
                'type' => 'text',
                'required' => true,
                'search' => true,
            ],
            [
                'name' => 'code',
                'type' => 'text',
                'required' => true,
                'search' => true,
            ],
            [
                'name' => 'country',
                'type' => 'text',
            ],
            [
                'name' => 'contact_name',
                'type' => 'text',
            ],
            [
                'name' => 'email',
                'type' => 'text',
            ],
            [
                'name' => 'phone',
                'type' => 'text',
            ],
            [
                'name' => 'currency',
                'type' => 'text',
                'default' => 'USD',
            ],
            [
                'name' => 'payment_terms',
                'type' => 'text',
            ],
            [
                'name' => 'is_active',
                'type' => 'toggle',
                'default' => true,
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
            'index' => ListBuyer::route('/'),
            'create' => CreateBuyer::route('/create'),
            'edit' => EditBuyer::route('/{record}/edit'),
        ];
    }
}

class ListBuyer extends ListRecords
{
    protected static string $resource = BuyerResource::class;

    protected function getHeaderActions(): array
    {
        return [\Filament\Actions\CreateAction::make()];
    }
}

class CreateBuyer extends CreateRecord
{
    protected static string $resource = BuyerResource::class;
}

class EditBuyer extends EditRecord
{
    protected static string $resource = BuyerResource::class;
}
