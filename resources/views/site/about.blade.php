@extends('layouts.site', ['title' => __('site.nav.about')])

@section('content')
<section class="mx-auto max-w-3xl px-5 py-16">
    <p class="sans text-xs uppercase tracking-[0.22em] text-emerald-900">Since 1998</p>
    <h1 class="mt-3 text-4xl">Two units. One size set. One shade standard.</h1>
    <div class="mt-6 space-y-4 text-lg text-stone-700">
        <p>Padma started as a cutting room that sold bundles to other sewing lines. The mill now runs its own preparatory, front, back, and assembly sections, plus a wash house for chino and jeans-style programs.</p>
        <p>The desk behind this site is the same flow the floor uses: buyer PO, tech pack, fabric roll, marker, bundle ticket, hourly output, end-line audit, carton, and shipment. Nothing is recorded that a cutting master or line chief would not already write in a book.</p>
    </div>
    <div class="mt-10 space-y-4">
        @foreach ($factories as $factory)
            <article class="rounded-2xl bg-white p-5">
                <h2 class="text-2xl">{{ $factory->name }}</h2>
                <p class="sans mt-1 text-sm text-stone-500">{{ $factory->city }} · {{ $factory->code }}</p>
                <p class="mt-3">{{ $factory->address }}</p>
            </article>
        @endforeach
    </div>
</section>
@endsection
