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
    <h1>Packing list {{ $shipment->shipment_no }}</h1>
    <p>প্যাকিং তালিকা · {{ $printed }}</p>
    <p>PO {{ $shipment->buyerOrder->po_number }} · {{ $shipment->buyerOrder->style->style_no }} · {{ $shipment->buyerOrder->buyer->name }}</p>
    <table>
        <thead><tr><th>Carton</th><th>Barcode</th><th>Colour</th><th>Size</th><th>Qty</th><th>Net kg</th><th>Gross kg</th></tr></thead>
        <tbody>
            @foreach ($shipment->cartons as $carton)
                <tr>
                    <td>{{ $carton->carton_no }}</td>
                    <td>{{ $carton->barcode }}</td>
                    <td>{{ $carton->color }}</td>
                    <td>{{ $carton->size }}</td>
                    <td>{{ $carton->quantity }}</td>
                    <td>{{ $carton->net_weight }}</td>
                    <td>{{ $carton->gross_weight }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
