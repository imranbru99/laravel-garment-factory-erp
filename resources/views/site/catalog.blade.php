@extends('layouts.site', ['title' => __('site.nav.catalog')])

@section('content')
<section class="mx-auto max-w-6xl px-5 py-16">
    <h1 class="text-4xl">Style book</h1>
    <form class="sans mt-6 flex flex-wrap gap-3 text-sm" method="get">
        <select name="category" class="rounded-full border border-stone-300 bg-white px-3 py-2" onchange="this.form.submit()">
            <option value="">{{ __('site.all') }} categories</option>
            @foreach ($categories as $value => $label)
                <option value="{{ $value }}" @selected(request('category') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <select name="fit" class="rounded-full border border-stone-300 bg-white px-3 py-2" onchange="this.form.submit()">
            <option value="">{{ __('site.all') }} fits</option>
            @foreach ($fits as $value => $label)
                <option value="{{ $value }}" @selected(request('fit') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <input name="fabric" value="{{ request('fabric') }}" placeholder="Fabric" class="rounded-full border border-stone-300 bg-white px-3 py-2">
        <input name="wash" value="{{ request('wash') }}" placeholder="Wash" class="rounded-full border border-stone-300 bg-white px-3 py-2">
        <button class="rounded-full bg-stone-900 px-4 py-2 text-stone-50">{{ __('site.filters') }}</button>
    </form>
    <div class="mt-8 grid gap-4 md:grid-cols-3">
        @forelse ($styles as $style)
            <a href="{{ route('styles.show', $style) }}" class="rounded-2xl bg-white p-5">
                <p class="sans text-xs uppercase tracking-wider text-emerald-900">{{ $style->style_no }} · {{ $style->buyer->name }}</p>
                <h2 class="mt-2 text-2xl">{{ $style->name }}</h2>
                <p class="sans mt-3 text-sm text-stone-600">{{ ucfirst($style->category) }} · {{ $style->fit }} fit · {{ $style->fabric_type }} · {{ $style->wash }}</p>
                <p class="mt-3 text-stone-700">SMV {{ $style->smv }} · {{ $style->composition }}</p>
            </a>
        @empty
            <p>No styles match those filters.</p>
        @endforelse
    </div>
</section>
@endsection
