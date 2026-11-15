@extends('layouts.site', ['title' => $post->title])

@section('content')
<article class="mx-auto max-w-3xl px-5 py-16">
    <p class="sans text-xs uppercase tracking-wider text-stone-500">{{ optional($post->published_at)->format('d M Y') }}</p>
    <h1 class="mt-3 text-4xl">{{ $post->title }}</h1>
    <div class="mt-6 space-y-4 text-lg text-stone-800">{!! nl2br(e($post->body)) !!}</div>
</article>
@endsection
