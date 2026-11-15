@extends('admin-panel::layouts.app')

@section('content')
@php
    $orders = \App\Models\BuyerOrder::query()->whereNotIn('status', ['closed', 'cancelled'])->count();
    $output = (int) \App\Models\ProductionLog::query()->whereDate('log_date', today())->sum('actual_qty');
    $issues = \App\Models\FactoryIssue::query()->whereIn('status', ['open', 'acknowledged', 'in_progress'])->count();
    $late = \App\Models\PurchaseOrder::query()->whereDate('expected_date', '<', today())->whereNotIn('status', ['received', 'closed'])->count();
@endphp
<div class="space-y-6">
    <div class="page-header">
        <div>
            <h1 class="page-title">{{ config('factory.name') }}</h1>
            <p class="page-subtitle">Live orders, today's sewing, open issues, and late material. The working desk is the factory admin.</p>
        </div>
        <div class="page-actions">
            <x-admin-panel::button variant="primary" icon="package" onclick="window.location='{{ url('/admin') }}'">
                Open factory admin
            </x-admin-panel::button>
        </div>
    </div>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:1rem;">
        <x-admin-panel::card><p>Open orders</p><strong style="font-size:1.75rem;">{{ $orders }}</strong></x-admin-panel::card>
        <x-admin-panel::card><p>Sewn today</p><strong style="font-size:1.75rem;">{{ number_format($output) }}</strong></x-admin-panel::card>
        <x-admin-panel::card><p>Open issues</p><strong style="font-size:1.75rem;">{{ $issues }}</strong></x-admin-panel::card>
        <x-admin-panel::card><p>Late material POs</p><strong style="font-size:1.75rem;">{{ $late }}</strong></x-admin-panel::card>
    </div>
</div>
@endsection
