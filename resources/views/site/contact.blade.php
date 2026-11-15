@extends('layouts.site', ['title' => __('site.nav.contact')])

@section('content')
<section class="mx-auto grid max-w-5xl gap-10 px-5 py-16 md:grid-cols-2">
    <div>
        <h1 class="text-4xl">Write to the desk</h1>
        <p class="mt-4 text-stone-700">Sample requests, buyer inquiries, and factory-tour visits land in the same merchandising queue.</p>
        <p class="sans mt-6 text-sm">Konabari, Gazipur 1740<br>desk@padmatrouser.test<br>+880 1700 000000</p>
    </div>
    <form method="post" action="{{ route('inquiries.store') }}" class="sans space-y-3 rounded-2xl bg-white p-5 text-sm">
        @csrf
        @if (session('sent'))
            <p class="rounded-xl bg-emerald-50 px-3 py-2 text-emerald-900">{{ __('site.sent') }}</p>
        @endif
        <label class="block">Type
            <select name="type" class="mt-1 w-full rounded-xl border border-stone-300 px-3 py-2">
                @foreach (['inquiry' => __('site.inquiry'), 'sample' => __('site.sample'), 'tour' => __('site.tour')] as $value => $label)
                    <option value="{{ $value }}" @selected(request('type', 'inquiry') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </label>
        <label class="block">Name <input name="name" required class="mt-1 w-full rounded-xl border border-stone-300 px-3 py-2" value="{{ old('name') }}"></label>
        <label class="block">Company <input name="company" class="mt-1 w-full rounded-xl border border-stone-300 px-3 py-2" value="{{ old('company') }}"></label>
        <label class="block">Email <input type="email" name="email" required class="mt-1 w-full rounded-xl border border-stone-300 px-3 py-2" value="{{ old('email') }}"></label>
        <label class="block">Phone <input name="phone" class="mt-1 w-full rounded-xl border border-stone-300 px-3 py-2" value="{{ old('phone') }}"></label>
        <label class="block">Style <input name="style_interest" class="mt-1 w-full rounded-xl border border-stone-300 px-3 py-2" value="{{ old('style_interest', request('style')) }}"></label>
        <label class="block">Message <textarea name="message" required rows="5" class="mt-1 w-full rounded-xl border border-stone-300 px-3 py-2">{{ old('message') }}</textarea></label>
        <button class="rounded-full bg-stone-900 px-5 py-2.5 text-stone-50">Send</button>
        @if ($errors->any())
            <ul class="text-red-800">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        @endif
    </form>
</section>
@endsection
