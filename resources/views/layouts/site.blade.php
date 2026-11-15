<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? __('site.mill') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,460;9..144,560&family=Outfit:wght@340;450;560&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.9/dist/cdn.min.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        serif: ['Fraunces', 'Georgia', 'serif'],
                        sans: ['Outfit', 'Segoe UI', 'sans-serif'],
                    },
                    colors: {
                        ink: '#101311',
                        paper: '#f4f0e6',
                        brass: '#c6a36a',
                    },
                },
            },
        };
    </script>
    <style>
        :root { color-scheme: light; }
        body { font-family: Fraunces, Georgia, serif; background: #f4f0e6; color: #161410; }
        .sans { font-family: Outfit, "Segoe UI", sans-serif; }
    </style>
</head>
@php($dark = trim($__env->yieldContent('theme')) === 'dark')
<body class="min-h-screen {{ $dark ? 'bg-ink text-paper' : 'bg-paper text-ink' }}" x-data="{ open: false }">
    <header class="sticky top-0 z-30 border-b {{ $dark ? 'border-white/10 bg-ink text-paper' : 'border-stone-300/80 bg-paper text-ink' }}">
        <div class="mx-auto flex max-w-7xl items-center justify-between gap-6 px-5 py-4">
            <a href="{{ route('home') }}" class="font-serif text-lg tracking-tight">{{ __('site.mill') }}</a>
            <nav class="sans hidden items-center gap-5 whitespace-nowrap text-[13px] tracking-wide lg:flex">
                <a class="hover:text-brass" href="{{ route('about') }}">{{ __('site.nav.about') }}</a>
                <a class="hover:text-brass" href="{{ route('capabilities') }}">{{ __('site.nav.capabilities') }}</a>
                <a class="hover:text-brass" href="{{ route('capacity') }}">{{ __('site.nav.capacity') }}</a>
                <a class="hover:text-brass" href="{{ route('certifications') }}">{{ __('site.nav.certifications') }}</a>
                <a class="hover:text-brass" href="{{ route('catalog') }}">{{ __('site.nav.catalog') }}</a>
                <a class="hover:text-brass" href="{{ route('blog') }}">{{ __('site.nav.news') }}</a>
                <a class="hover:text-brass" href="{{ route('contact') }}">{{ __('site.nav.contact') }}</a>
            </nav>
            <div class="sans flex items-center gap-3 text-[11px] uppercase tracking-[0.16em]">
                <a href="{{ route('locale', 'en') }}" @class(['text-brass' => app()->getLocale() === 'en'])>EN</a>
                <a href="{{ route('locale', 'bn') }}" @class(['text-brass' => app()->getLocale() === 'bn'])>বাং</a>
                <a href="{{ url('/admin') }}" class="hidden rounded-full bg-brass px-3.5 py-2 text-ink sm:inline">{{ __('site.nav.admin') }}</a>
                <button type="button" class="-mr-2 p-2 lg:hidden" @click="open = !open" aria-label="Menu">
                    <span class="block h-px w-5 bg-current"></span>
                    <span class="mt-1.5 block h-px w-5 bg-current"></span>
                </button>
            </div>
        </div>
        <nav x-show="open" x-cloak class="sans border-t px-5 py-4 text-sm lg:hidden {{ $dark ? 'border-white/10' : 'border-stone-300' }}">
            <div class="flex flex-col gap-3">
                <a href="{{ route('about') }}">{{ __('site.nav.about') }}</a>
                <a href="{{ route('capabilities') }}">{{ __('site.nav.capabilities') }}</a>
                <a href="{{ route('capacity') }}">{{ __('site.nav.capacity') }}</a>
                <a href="{{ route('certifications') }}">{{ __('site.nav.certifications') }}</a>
                <a href="{{ route('catalog') }}">{{ __('site.nav.catalog') }}</a>
                <a href="{{ route('blog') }}">{{ __('site.nav.news') }}</a>
                <a href="{{ route('contact') }}">{{ __('site.nav.contact') }}</a>
                <a href="{{ url('/admin') }}">{{ __('site.nav.admin') }}</a>
            </div>
        </nav>
    </header>
    <main>
        @yield('content')
    </main>
    <footer class="sans border-t px-5 py-10 text-sm {{ $dark ? 'border-white/10 text-stone-400' : 'border-stone-300 text-stone-600' }}">
        <div class="mx-auto flex max-w-7xl flex-wrap items-end justify-between gap-6">
            <div>
                <p class="font-serif text-2xl {{ $dark ? 'text-paper' : 'text-ink' }}">{{ __('site.mill') }}</p>
                <p class="mt-2">Konabari, Gazipur · Tongi, Ashulia</p>
            </div>
            <p>desk@padmatrouser.test</p>
        </div>
    </footer>
    <style>[x-cloak]{display:none !important}</style>
</body>
</html>
