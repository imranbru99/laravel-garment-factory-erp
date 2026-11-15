<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use ImranDevBD\LaravelAdminPanel\Navigation\NavigationGroup;
use ImranDevBD\LaravelAdminPanel\Navigation\NavigationItem;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->app->booted(function () {
            if (! $this->app->bound(\ImranDevBD\LaravelAdminPanel\AdminPanel::class)) {
                return;
            }

            $panel = admin_panel('admin');
            $panel->brandName((string) config('factory.name'));
            $panel->navigation([
                NavigationItem::make('Factory pulse')
                    ->icon('layout-dashboard')
                    ->url($panel->url('/')),
                NavigationGroup::make('Open a desk')->items([
                    NavigationItem::make('Factory admin')->icon('package')->url(url('/admin')),
                    NavigationItem::make('AI Hub')->icon('sparkles')->url(url('/ai-hub')),
                    NavigationItem::make('Style catalog AI')->icon('palette')->url(url('/ai-catalog')),
                    NavigationItem::make('Document OCR')->icon('folder')->url(url('/ai-ocr')),
                    NavigationItem::make('Blueprint studio')->icon('settings')->url(url('/blueprint-studio')),
                ]),
            ]);
        });
    }
}
