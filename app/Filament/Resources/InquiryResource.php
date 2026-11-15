<?php

namespace App\Filament\Resources;

use App\Filament\Support\FieldBuilder;
use App\Filament\Support\GuardsModule;
use App\Models\Inquiry;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class InquiryResource extends Resource
{
    use GuardsModule;

    protected static ?string $model = Inquiry::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';

    protected static ?string $navigationGroup = 'Website';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Inquiry';

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
                'name' => 'type',
                'type' => 'select',
                'options' => [
                    'inquiry' => 'Inquiry',
                    'sample' => 'Sample request',
                    'tour' => 'Factory tour',
                ],
                'default' => 'inquiry',
                'filter' => true,
                'badge' => true,
            ],
            [
                'name' => 'name',
                'type' => 'text',
                'required' => true,
                'search' => true,
            ],
            [
                'name' => 'company',
                'type' => 'text',
            ],
            [
                'name' => 'email',
                'type' => 'text',
                'required' => true,
            ],
            [
                'name' => 'phone',
                'type' => 'text',
            ],
            [
                'name' => 'style_interest',
                'type' => 'text',
            ],
            [
                'name' => 'status',
                'type' => 'select',
                'options' => [
                    'new' => 'New',
                    'contacted' => 'Contacted',
                    'closed' => 'Closed',
                ],
                'default' => 'new',
                'filter' => true,
                'badge' => true,
            ],
            [
                'name' => 'locale',
                'type' => 'text',
            ],
            [
                'name' => 'message',
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
            'index' => ListInquiry::route('/'),
            'create' => CreateInquiry::route('/create'),
            'edit' => EditInquiry::route('/{record}/edit'),
        ];
    }
}

class ListInquiry extends ListRecords
{
    protected static string $resource = InquiryResource::class;

    protected function getHeaderActions(): array
    {
        return [\Filament\Actions\CreateAction::make()];
    }
}

class CreateInquiry extends CreateRecord
{
    protected static string $resource = InquiryResource::class;
}

class EditInquiry extends EditRecord
{
    protected static string $resource = InquiryResource::class;
}
