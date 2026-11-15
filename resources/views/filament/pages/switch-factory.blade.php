<x-filament-panels::page>
    <form wire:submit="save" class="space-y-4 max-w-xl">
        {{ $this->form }}
        <x-filament::button type="submit">Use this factory</x-filament::button>
    </form>
</x-filament-panels::page>
