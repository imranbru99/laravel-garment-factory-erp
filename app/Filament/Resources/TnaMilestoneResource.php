<?php

namespace App\Filament\Resources;

use App\Filament\Support\FieldBuilder;
use App\Filament\Support\GuardsModule;
use App\Models\TnaMilestone;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TnaMilestoneResource extends Resource
{
    use GuardsModule;

    protected static ?string $model = TnaMilestone::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationGroup = 'Merchandising';

    protected static ?int $navigationSort = 9;

    protected static ?string $navigationLabel = 'TNA';

    public static function module(): string
    {
        return 'orders';
    }

    public static function roles(): array
    {
        return [
    'merchandiser',
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
                'name' => 'buyer_order_id',
                'type' => 'relation',
                'relation' => 'buyerOrder',
                'title' => 'po_number',
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
                'name' => 'department',
                'type' => 'text',
            ],
            [
                'name' => 'planned_date',
                'type' => 'date',
            ],
            [
                'name' => 'actual_date',
                'type' => 'date',
            ],
            [
                'name' => 'status',
                'type' => 'select',
                'options' => [
                    'pending' => 'Pending',
                    'done' => 'Done',
                    'delayed' => 'Delayed',
                ],
                'default' => 'pending',
                'filter' => true,
                'badge' => true,
            ],
            [
                'name' => 'owner',
                'type' => 'text',
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
            'index' => ListTnaMilestone::route('/'),
            'create' => CreateTnaMilestone::route('/create'),
            'edit' => EditTnaMilestone::route('/{record}/edit'),
        ];
    }
}

class ListTnaMilestone extends ListRecords
{
    protected static string $resource = TnaMilestoneResource::class;

    protected function getHeaderActions(): array
    {
        return [\Filament\Actions\CreateAction::make()];
    }
}

class CreateTnaMilestone extends CreateRecord
{
    protected static string $resource = TnaMilestoneResource::class;
}

class EditTnaMilestone extends EditRecord
{
    protected static string $resource = TnaMilestoneResource::class;
}
