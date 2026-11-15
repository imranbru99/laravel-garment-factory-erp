@extends('layouts.site', ['title' => __('site.nav.news')])

@section('content')
<section class="mx-auto max-w-3xl px-5 py-16">
    <h1 class="text-4xl">{{ __('site.nav.news') }}</h1>
    <div class="mt-8 space-y-6">
        @foreach ($posts as $post)
            <a href="{{ route('posts.show', $post) }}" class="block border-t border-stone-300 pt-4">
                <p class="sans text-xs uppercase tracking-wider text-stone-500">{{ optional($post->published_at)->format('d M Y') }} · {{ strtoupper($post->locale) }}</p>
                <h2 class="mt-2 text-2xl">{{ $post->title }}</h2>
                <p class="mt-2 text-stone-700">{{ $post->excerpt }}</p>
            </a>
        @endforeach
    </div>
</section>
@endsection
