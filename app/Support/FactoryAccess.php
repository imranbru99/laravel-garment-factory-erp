<?php

namespace App\Support;

use App\Models\User;

class FactoryAccess
{
    public static function moduleEnabled(string $module): bool
    {
        return (bool) config('factory.modules.'.$module, false);
    }

    public static function allows(?User $user, string $module, array $roles): bool
    {
        if (! self::moduleEnabled($module) || $user === null) {
            return false;
        }

        if ($user->role === 'admin') {
            return true;
        }

        return in_array($user->role, $roles, true);
    }
}
