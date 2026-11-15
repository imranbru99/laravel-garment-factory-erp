<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ApplyFactoryContext
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if ($user && $user->factory_id && ! $request->session()->has('factory_id')) {
            $request->session()->put('factory_id', $user->factory_id);
        }

        if ($user?->locale) {
            app()->setLocale($user->locale);
        }

        return $next($request);
    }
}
