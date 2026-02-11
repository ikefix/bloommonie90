<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: DejaVu Sans; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #000; padding: 6px; }
        th { background: #f2f2f2; }
        h2, h4 { text-align: center; margin: 0; }
        .filters { margin-top: 10px; text-align: center; }
    </style>
</head>
<body>

<h2>Sales Report</h2>

<div class="filters">
    @if($startDate && $endDate)
        <p><strong>Date Range:</strong> {{ $startDate }} → {{ $endDate }}</p>
    @endif

    @if($shop)
        <p><strong>Shop:</strong> {{ $shop->name }}</p>
    @else
        <p><strong>Shop:</strong> All Shops</p>
    @endif
</div>

<hr>

<p><strong>Total Sales:</strong> ₦{{ number_format($totalSales, 2) }}</p>
<p><strong>Total Transactions:</strong> {{ $totalTransactions }}</p>

<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Product</th>
            <th>Units Sold</th>
        </tr>
    </thead>
    <tbody>
        @foreach($topProducts as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->product_name }}</td>
                <td>{{ $item->total_sold }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

</body>
</html>
