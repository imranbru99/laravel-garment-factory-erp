@extends('layouts.site', ['title' => __('site.nav.capacity')])

@section('content')
<section class="mx-auto max-w-4xl px-5 py-16">
    <h1 class="text-4xl">Capacity the planner can load</h1>
    <p class="mt-4 text-lg text-stone-700">Figures are manned lines at the standard 10-hour shift, before overtime. The planning desk balances load against these numbers every morning.</p>
    <div class="mt-8 overflow-hidden rounded-2xl bg-white">
        <table class="sans w-full text-left text-sm">
            <thead class="bg-stone-900 text-stone-50">
                <tr>
                    <th class="px-4 py-3">Unit</th>
                    <th class="px-4 py-3">Lines</th>
                    <th class="px-4 py-3">Pieces / day</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($factories as $factory)
                    <tr class="border-t border-stone-200">
                        <td class="px-4 py-3">{{ $factory->name }}</td>
                        <td class="px-4 py-3">{{ $factory->sewing_lines }}</td>
                        <td class="px-4 py-3">{{ number_format($factory->daily_capacity_pcs) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</section>
@endsection
