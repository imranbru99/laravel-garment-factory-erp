<?php

namespace App\Filament\Pages;

use App\Models\Factory;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class SwitchFactory extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';

    protected static ?string $navigationGroup = 'Organization';

    protected static ?string $navigationLabel = 'Working factory';

    protected static ?int $navigationSort = 0;

    protected static string $view = 'filament.pages.switch-factory';

    public ?array $data = [];

    public static function canAccess(): bool
    {
        return auth()->user()?->role === 'admin';
    }

    public function mount(): void
    {
        $this->form->fill([
            'factory_id' => session('factory_id'),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Select::make('factory_id')
                ->label('Factory')
                ->options(fn () => Factory::query()->orderBy('name')->pluck('name', 'id'))
                ->placeholder('All factories')
                ->helperText('Admins can leave this empty to see every unit. Other roles stay on their own factory.'),
        ])->statePath('data');
    }

    public function save(): void
    {
        $factoryId = $this->form->getState()['factory_id'] ?? null;
        session(['factory_id' => $factoryId ?: null]);

        if (! $factoryId) {
            session()->forget('factory_id');
        }

        Notification::make()->title('Working factory updated')->success()->send();
    }
}
