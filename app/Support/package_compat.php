<?php

declare(strict_types=1);

/**
 * imrandevbd/laravel-filament-master still imports the Filament v2/early-v3
 * trait path. Filament v3.3 keeps the same trait under the Support namespace.
 */
if (! trait_exists(\Filament\Commands\Concerns\CanManipulateFiles::class, false)
    && trait_exists(\Filament\Support\Commands\Concerns\CanManipulateFiles::class)) {
    class_alias(
        \Filament\Support\Commands\Concerns\CanManipulateFiles::class,
        \Filament\Commands\Concerns\CanManipulateFiles::class,
    );
}
