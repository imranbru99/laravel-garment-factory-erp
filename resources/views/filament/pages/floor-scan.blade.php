<x-filament-panels::page>
    <form wire:submit="scan" class="space-y-4 max-w-xl">
        {{ $this->form }}
        <x-filament::button type="submit">Record scan</x-filament::button>
    </form>
    @if ($result)
        <p class="mt-6 text-sm">{{ $result }}</p>
    @endif
</x-filament-panels::page>
