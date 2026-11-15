<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: "Noto Sans", "Noto Sans Bengali", sans-serif; font-size: 12px; }
        h1 { font-size: 18px; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #222; padding: 4px 6px; }
    </style>
</head>
<body>
    <p>{{ $shipment->factory->company->name ?? $shipment->factory->name }}</p>
    <h1>Commercial invoice {{ $shipment->invoice_no }}</h1>
    <p>বাণিজ্যিক চালান · {{ $printed }}</p>
    <p>
        Buyer {{ $shipment->buyerOrder->buyer->name }} ·
        PO {{ $shipment->buyerOrder->po_number }} ·
        Style {{ $shipment->buyerOrder->style->style_no }} {{ $shipment->buyerOrder->style->name }}
    </p>
    <p>Shipment {{ $shipment->shipment_no }} · {{ strtoupper($shipment->mode) }} · {{ $shipment->vessel }} · Container {{ $shipment->container_no }}</p>
    <p>ETD {{ optional($shipment->etd)->format('d M Y') }} · ETA {{ optional($shipment->eta)->format('d M Y') }} · {{ $shipment->destination }}</p>
    <table>
        <thead><tr><th>Colour</th><th>Size</th><th>Quantity</th><th>FOB</th><th>Amount</th></tr></thead>
        <tbody>
            @foreach ($shipment->buyerOrder->matrixLines as $line)
                <tr>
                    <td>{{ $line->color }}</td>
                    <td>{{ $line->size }}</td>
                    <td>{{ $line->quantity }}</td>
                    <td>{{ number_format((float) $shipment->buyerOrder->fob_price, 2) }}</td>
                    <td>{{ number_format($line->quantity * (float) $shipment->buyerOrder->fob_price, 2) }} {{ $shipment->buyerOrder->currency }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
