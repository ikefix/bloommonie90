<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Goods That Made Profit</title>

    <style>
        body { font-family: DejaVu Sans; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 6px; }
        th { background: #f1f1f1; }
        h2 { margin-bottom: 10px; }
        .totals-row {
            font-weight: bold;
            background: #f9f9f9;
        }
        .summary {
            margin-top: 15px;
            font-weight: bold;
        }
    </style>
</head>
<body>

<h2>Goods That Made Profit</h2>

<p>
    Period:
    {{ $request->start_date ?? 'Beginning' }}
    —
    {{ $request->end_date ?? 'Today' }}
</p>

@php
    $totalRevenue = 0;
    $totalCost = 0;
    $totalProfit = 0;
@endphp

<table>
    <thead>
        <tr>
            <th>Product</th>
            <th>Qty Sold</th>
            <th>Revenue (₦)</th>
            <th>Cost (₦)</th>
            <th>Profit (₦)</th>
        </tr>
    </thead>
    <tbody>
        @foreach($goodsByProfit as $item)
            @php
                $totalRevenue += $item['revenue'];
                $totalCost += $item['cost'];
                $totalProfit += $item['profit'];
            @endphp
            <tr>
                <td>{{ $item['product'] }}</td>
                <td>{{ $item['quantity'] }}</td>
                <td>{{ number_format($item['revenue'], 2) }}</td>
                <td>{{ number_format($item['cost'], 2) }}</td>
                <td>{{ number_format($item['profit'], 2) }}</td>
            </tr>
        @endforeach

        <!-- Totals Row -->
        <tr class="totals-row">
            <td colspan="2">TOTAL</td>
            <td>{{ number_format($totalRevenue, 2) }}</td>
            <td>{{ number_format($totalCost, 2) }}</td>
            <td>{{ number_format($totalProfit, 2) }}</td>
        </tr>
    </tbody>
</table>

</body>
</html>