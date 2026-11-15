<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: "Noto Sans", "Noto Sans Bengali", sans-serif; font-size: 12px; color: #111; }
        h1 { font-size: 18px; margin: 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #222; padding: 4px 6px; text-align: left; }
        .muted { color: #444; }
    </style>
</head>
<body>
    <p class="muted">{{ $plan->factory->name ?? '' }} · কাট টিকিট / Cut ticket · {{ $printed }}</p>
    <h1>Lay {{ $plan->lay_no }}</h1>
    <p>
        Order {{ $plan->buyerOrder->po_number }} ·
        {{ $plan->buyerOrder->buyer->name }} ·
        {{ $plan->marker->style->style_no ?? '' }} {{ $plan->marker->style->name ?? '' }}
    </p>
    <p>Shade {{ $plan->shade }} · Lot {{ $plan->lot }} · Plies {{ $plan->plies }} · Cut qty {{ $plan->cut_qty }} · Marker efficiency {{ $plan->marker->efficiency }}%</p>
    <table>
        <thead>
            <tr><th>Bundle</th><th>QR</th><th>Colour</th><th>Size</th><th>Qty</th><th>Shade</th><th>Parts</th></tr>
        </thead>
        <tbody>
            @foreach ($plan->bundles as $bundle)
                <tr>
                    <td>{{ $bundle->bundle_no }}</td>
                    <td>{{ $bundle->qr_code }}</td>
                    <td>{{ $bundle->color }}</td>
                    <td>{{ $bundle->size }}</td>
                    <td>{{ $bundle->quantity }}</td>
                    <td>{{ $bundle->shade }}</td>
                    <td>{{ $bundle->components->pluck('component')->implode(', ') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
