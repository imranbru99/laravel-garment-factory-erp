@extends('layouts.site', ['title' => __('site.nav.certifications')])

@section('content')
<section class="mx-auto max-w-3xl px-5 py-16">
    <h1 class="text-4xl">Certificates on the wall, and in the file</h1>
    <div class="mt-8 space-y-4">
        @forelse ($certifications as $certificate)
            <article class="rounded-2xl bg-white p-5">
                <h2 class="text-2xl">{{ $certificate->name }}</h2>
                <p class="sans mt-1 text-sm text-stone-500">{{ $certificate->issuer }} · {{ $certificate->certificate_no }}</p>
                <p class="mt-3">{{ $certificate->summary }}</p>
                @if ($certificate->valid_until)
                    <p class="sans mt-2 text-xs uppercase tracking-wider text-stone-500">Valid until {{ $certificate->valid_until->format('d M Y') }}</p>
                @endif
            </article>
        @empty
            <p>Certificates are being filed.</p>
        @endforelse
    </div>
</section>
@endsection
