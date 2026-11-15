@extends('layouts.site', ['title' => $style->style_no.' '.$style->name])

@section('content')
<section class="mx-auto max-w-3xl px-5 py-16">
    <p class="sans text-xs uppercase tracking-[0.22em] text-emerald-900">{{ $style->style_no }} · {{ $style->buyer->name }}</p>
    <h1 class="mt-3 text-4xl">{{ $style->name }}</h1>
    <p class="mt-4 text-lg text-stone-700">{{ $style->description }}</p>
    <dl class="sans mt-8 grid grid-cols-2 gap-4 text-sm">
        <div><dt class="text-stone-500">Category</dt><dd>{{ ucfirst($style->category) }}</dd></div>
        <div><dt class="text-stone-500">Fit</dt><dd>{{ ucfirst((string) $style->fit) }}</dd></div>
        <div><dt class="text-stone-500">Fabric</dt><dd>{{ $style->fabric_type }}</dd></div>
        <div><dt class="text-stone-500">Wash</dt><dd>{{ $style->wash ?: 'None' }}</dd></div>
        <div><dt class="text-stone-500">GSM</dt><dd>{{ $style->gsm }}</dd></div>
        <div><dt class="text-stone-500">SMV</dt><dd>{{ $style->smv }}</dd></div>
    </dl>
    @if ($style->ai_listing)
        <article class="mt-8 rounded-2xl bg-white p-5">
            <h2 class="text-2xl">Listing draft</h2>
            <p class="mt-2">{{ $style->ai_listing['summary'] ?? $style->ai_listing['title'] ?? '' }}</p>
        </article>
    @endif
    @if ($style->techPacks->isNotEmpty())
        <h2 class="mt-10 text-2xl">Tech pack</h2>
        @foreach ($style->techPacks as $pack)
            <p class="sans mt-2 text-sm text-stone-500">Version {{ $pack->version }} · {{ $pack->status }}</p>
            <p class="mt-2">{{ $pack->construction_notes }}</p>
        @endforeach
    @endif
    <a href="{{ route('contact') }}?type=sample&style={{ $style->style_no }}" class="sans mt-8 inline-block rounded-full bg-emerald-900 px-5 py-2.5 text-sm text-stone-50">{{ __('site.request_sample') }}</a>
</section>
@endsection
