<?php

namespace App\Filament\Support;

use App\Support\FactoryAccess;

trait GuardsModule
{
    abstract public static function module(): string;

    abstract public static function roles(): array;

    public static function canViewAny(): bool
    {
        $user = auth()->user();

        return $user !== null && FactoryAccess::allows($user, static::module(), static::roles());
    }

    public static function canCreate(): bool
    {
        $user = auth()->user();

        return static::canViewAny() && $user?->role !== 'buyer' && static::allowsChanges();
    }

    public static function allowsChanges(): bool
    {
        return true;
    }
}
