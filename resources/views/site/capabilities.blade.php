@extends('layouts.site', ['title' => __('site.nav.capabilities')])

@section('content')
<section class="mx-auto max-w-4xl px-5 py-16">
    <h1 class="text-4xl">What the floor can actually make</h1>
    <div class="mt-8 grid gap-4 md:grid-cols-2">
        @foreach ([
            ['Formal trousers', 'Pleat, flat-front, wool-blend and poly-viscose dress pants with a fused waistband.'],
            ['Chinos', 'Stretch cotton and garment wash, including enzyme and tint recipes.'],
            ['Cargo', 'Utility pockets, bartack load points, and relaxed blocks.'],
            ['Jeans-style', 'Five-pocket construction without a denim laundry. Rigid and comfort stretch.'],
            ['Samples', 'Proto, fit, size set, and pre-production, with buyer comments kept on the style.'],
            ['Compliance file', 'Packing list, commercial invoice, and certificate data from the same shipment.'],
        ] as [$title, $body])
            <article class="rounded-2xl bg-white p-5">
                <h2 class="text-2xl">{{ $title }}</h2>
                <p class="mt-2 text-stone-700">{{ $body }}</p>
            </article>
        @endforeach
    </div>
</section>
@endsection
