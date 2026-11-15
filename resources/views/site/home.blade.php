@extends('layouts.site')

@section('content')
@php
    $cloth = [
        'formal' => 'linear-gradient(145deg, #24323c 0%, #10161b 70%)',
        'chino' => 'linear-gradient(145deg, #c4a574 0%, #6e5430 72%)',
        'cargo' => 'linear-gradient(145deg, #5d6848 0%, #2a3124 72%)',
        'jeans' => 'linear-gradient(145deg, #31445f 0%, #141c28 72%)',
        'casual' => 'linear-gradient(145deg, #8a6a55 0%, #3d2c24 72%)',
    ];
    $daily = (int) $factories->sum('daily_capacity_pcs');
    $lines = (int) $factories->sum('sewing_lines');
    $featured = $styles->first();
    $rest = $styles->skip(1);
@endphp

<section class="relative overflow-hidden border-b border-stone-300/80">
    <div class="mx-auto grid max-w-7xl gap-10 px-5 pb-16 pt-14 lg:grid-cols-12 lg:items-end lg:pt-20">
        <div class="lg:col-span-7">
            <p class="sans text-[11px] uppercase tracking-[0.32em] text-stone-500">{{ __('site.hero_kicker') }}</p>
            <h1 class="mt-6 max-w-4xl text-[3.1rem] leading-[0.95] tracking-[-0.03em] md:text-7xl lg:text-[5.4rem]">{{ __('site.hero_title') }}</h1>
        </div>
        <div class="lg:col-span-5 lg:pb-2">
            <p class="max-w-md text-lg leading-relaxed text-stone-700">{{ __('site.hero_body') }}</p>
            <div class="sans mt-8 flex flex-wrap items-center gap-x-6 gap-y-3 text-sm">
                <a href="{{ route('catalog') }}" class="group inline-flex items-center gap-2 border-b border-ink pb-1">
                    {{ __('site.view_styles') }}
                    <span class="transition group-hover:translate-x-1">→</span>
                </a>
                <a href="{{ route('contact') }}?type=sample" class="text-stone-600 underline decoration-stone-300 underline-offset-4 hover:text-ink">{{ __('site.request_sample') }}</a>
            </div>
        </div>
    </div>

    @if ($featured)
        <a href="{{ route('styles.show', $featured) }}" class="group mx-auto grid max-w-7xl overflow-hidden px-5 pb-8 lg:grid-cols-12">
            <div class="relative min-h-[22rem] lg:col-span-8 lg:min-h-[34rem]" style="background: {{ $cloth[$featured->category] ?? $cloth['formal'] }}">
                <div class="absolute inset-0 opacity-40" style="background-image: repeating-linear-gradient(32deg, rgba(255,255,255,.18) 0 1px, transparent 1px 7px), repeating-linear-gradient(-32deg, rgba(0,0,0,.18) 0 1px, transparent 1px 11px);"></div>
                <div class="absolute inset-x-0 bottom-0 flex items-end justify-between p-6 text-white md:p-8">
                    <p class="sans text-[11px] uppercase tracking-[0.28em]">{{ $featured->style_no }}</p>
                    <p class="sans text-[11px] uppercase tracking-[0.28em] text-white/70">{{ ucfirst($featured->category) }}</p>
                </div>
            </div>
            <div class="flex flex-col justify-between border border-t-0 border-stone-300 bg-white p-6 lg:col-span-4 lg:border-l-0 lg:border-t lg:p-8">
                <p class="sans text-[11px] uppercase tracking-[0.22em] text-stone-500">{{ __('site.book_kicker') }}</p>
                <div>
                    <h2 class="text-4xl leading-none tracking-tight group-hover:translate-x-1 transition">{{ $featured->name }}</h2>
                    <p class="sans mt-4 text-sm text-stone-600">{{ $featured->fit }} fit · {{ $featured->fabric_type }}@if($featured->wash) · {{ $featured->wash }}@endif</p>
                    <p class="sans mt-8 text-xs uppercase tracking-[0.2em] text-stone-500">SMV {{ $featured->smv }}</p>
                </div>
            </div>
        </a>
    @endif
</section>

<section class="border-b border-stone-300/80">
    <div class="mx-auto grid max-w-7xl grid-cols-2 divide-stone-300 md:grid-cols-4 md:divide-x">
        <div class="px-5 py-8">
            <p class="text-4xl tracking-tight">{{ number_format($daily) }}</p>
            <p class="sans mt-2 text-[11px] uppercase tracking-[0.18em] text-stone-500">{{ __('site.capacity_label') }}</p>
        </div>
        <div class="border-l border-stone-300 px-5 py-8 md:border-l-0">
            <p class="text-4xl tracking-tight">{{ $lines }}</p>
            <p class="sans mt-2 text-[11px] uppercase tracking-[0.18em] text-stone-500">{{ __('site.lines_label') }}</p>
        </div>
        <div class="border-t border-stone-300 px-5 py-8 md:border-t-0">
            <p class="text-4xl tracking-tight">{{ $factories->count() }}</p>
            <p class="sans mt-2 text-[11px] uppercase tracking-[0.18em] text-stone-500">{{ $factories->pluck('city')->filter()->implode(' · ') }}</p>
        </div>
        <div class="border-l border-t border-stone-300 px-5 py-8 md:border-t-0">
            <p class="text-4xl tracking-tight">{{ $certifications->count() }}</p>
            <p class="sans mt-2 text-[11px] uppercase tracking-[0.18em] text-stone-500">{{ __('site.nav.certifications') }}</p>
        </div>
    </div>
</section>

<section class="border-b border-stone-300/80">
    <div class="mx-auto max-w-7xl px-5 py-16">
        <p class="sans text-[11px] uppercase tracking-[0.28em] text-stone-500">{{ __('site.process_kicker') }}</p>
        <h2 class="mt-4 max-w-2xl text-4xl tracking-tight md:text-5xl">{{ __('site.process_title') }}</h2>
        <div class="mt-12 grid gap-px bg-stone-300 md:grid-cols-4">
            @foreach (__('site.process') as $index => $step)
                <article class="bg-paper p-6">
                    <p class="sans text-[11px] tracking-[0.24em] text-stone-400">0{{ $index + 1 }}</p>
                    <h3 class="mt-6 text-2xl">{{ $step['title'] }}</h3>
                    <p class="sans mt-3 text-sm leading-relaxed text-stone-600">{{ $step['body'] }}</p>
                </article>
            @endforeach
        </div>
    </div>
</section>

@if ($rest->isNotEmpty())
<section class="border-b border-stone-300/80">
    <div class="mx-auto max-w-7xl px-5 py-16">
        <div class="flex items-end justify-between gap-6">
            <h2 class="text-4xl tracking-tight md:text-5xl">{{ __('site.book_title') }}</h2>
            <a class="sans hidden text-sm underline decoration-stone-400 underline-offset-4 sm:inline" href="{{ route('catalog') }}">{{ __('site.nav.catalog') }}</a>
        </div>
        <div class="mt-10 grid gap-4 md:grid-cols-3">
            @foreach ($rest as $style)
                <a href="{{ route('styles.show', $style) }}" class="group block">
                    <div class="relative aspect-[4/5] overflow-hidden" style="background: {{ $cloth[$style->category] ?? $cloth['formal'] }}">
                        <div class="absolute inset-0 opacity-30" style="background-image: repeating-linear-gradient(115deg, rgba(255,255,255,.25) 0 1px, transparent 1px 8px);"></div>
                        <p class="sans absolute left-4 top-4 text-[11px] uppercase tracking-[0.22em] text-white/80">{{ $style->style_no }}</p>
                    </div>
                    <div class="mt-4 flex items-baseline justify-between gap-3">
                        <h3 class="text-2xl tracking-tight group-hover:underline">{{ $style->name }}</h3>
                        <span class="sans text-[11px] uppercase tracking-[0.16em] text-stone-500">{{ $style->fit }}</span>
                    </div>
                    <p class="sans mt-1 text-sm text-stone-600">{{ ucfirst($style->category) }} · {{ $style->fabric_type }}</p>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif

<section>
    <div class="mx-auto grid max-w-7xl gap-14 px-5 py-16 lg:grid-cols-12">
        <div class="lg:col-span-7">
            <p class="sans text-[11px] uppercase tracking-[0.28em] text-stone-500">{{ __('site.news_kicker') }}</p>
            <div class="mt-6">
                @foreach ($posts as $post)
                    <a href="{{ route('posts.show', $post) }}" class="group flex items-end justify-between gap-6 border-t border-stone-300 py-6">
                        <h3 class="text-2xl tracking-tight group-hover:underline md:text-3xl">{{ $post->title }}</h3>
                        <span class="sans shrink-0 text-[11px] uppercase tracking-[0.16em] text-stone-500">{{ optional($post->published_at)->format('d M Y') }}</span>
                    </a>
                @endforeach
            </div>
        </div>
        <div class="lg:col-span-4 lg:col-start-9">
            <p class="sans text-[11px] uppercase tracking-[0.28em] text-stone-500">{{ __('site.nav.certifications') }}</p>
            <ul class="mt-6 divide-y divide-stone-300 border-y border-stone-300">
                @foreach ($certifications as $certificate)
                    <li class="py-4">
                        <p class="text-xl">{{ $certificate->name }}</p>
                        <p class="sans mt-1 text-[11px] uppercase tracking-[0.16em] text-stone-500">{{ $certificate->issuer }}</p>
                    </li>
                @endforeach
            </ul>
            <div class="mt-8 space-y-2">
                @foreach ($factories as $factory)
                    <p class="flex justify-between text-sm">
                        <span>{{ $factory->city }}</span>
                        <span class="sans text-stone-500">{{ number_format($factory->daily_capacity_pcs) }}</span>
                    </p>
                @endforeach
            </div>
        </div>
    </div>
</section>

<section class="bg-ink text-paper">
    <div class="mx-auto flex max-w-7xl flex-col justify-between gap-10 px-5 py-20 md:flex-row md:items-end">
        <h2 class="max-w-3xl text-4xl leading-[1.05] tracking-tight md:text-6xl">{{ __('site.cta_title') }}</h2>
        <div class="sans flex flex-col gap-3 text-sm">
            <a href="{{ route('contact') }}?type=sample" class="inline-flex items-center justify-between gap-8 border border-white/20 px-5 py-4 hover:bg-white hover:text-ink">{{ __('site.request_sample') }} <span>→</span></a>
            <a href="{{ route('contact') }}?type=tour" class="inline-flex items-center justify-between gap-8 border border-white/20 px-5 py-4 hover:bg-white hover:text-ink">{{ __('site.tour') }} <span>→</span></a>
        </div>
    </div>
    <p class="sans mx-auto max-w-7xl px-5 pb-10 text-sm text-stone-400">{{ __('site.cta_body') }}</p>
</section>
@endsection
