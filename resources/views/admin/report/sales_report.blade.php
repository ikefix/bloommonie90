@extends('layouts.adminapp')

@section('admincontent')
<div class="container-fluid">

    {{-- PAGE TITLE --}}
    <div class="mb-4">
        <h3 class="fw-bold text-primary">Sales Report</h3>
        <p class="text-muted">Overview of sales, top products, and daily trends</p>
    </div>

    {{-- FILTERS --}}
    <form method="GET" action="{{ route('admin.report.sales_report') }}" class="row g-3 mb-4 align-items-end">

        <div class="col-md-3">
            <label class="form-label">Start Date</label>
            <input type="date" name="start_date" class="form-control"
                value="{{ request('start_date') }}">
        </div>

        <div class="col-md-3">
            <label class="form-label">End Date</label>
            <input type="date" name="end_date" class="form-control"
                value="{{ request('end_date') }}">
        </div>

        <div class="col-md-3">
            <label class="form-label">Shop</label>
            <select name="shop_id" class="form-select">
                <option value="">All Shops</option>
                @foreach($shops as $shop)
                    <option value="{{ $shop->id }}"
                        {{ request('shop_id') == $shop->id ? 'selected' : '' }}>
                        {{ $shop->name }}
                    </option>
                @endforeach
            </select>

        </div>

        <div class="col-md-3 d-grid">
            <button class="btn btn-primary">Apply Filter</button>
        </div>

    </form>

    {{-- SUMMARY CARDS --}}
    <div class="row mb-4 g-3">

        <div class="col-md-4">
            <div class="card shadow-sm border-0 p-3 text-center bg-light">
                <h6 class="text-muted">Total Sales</h6>
                <h4 class="fw-bold text-success">₦{{ number_format($totalSales ?? 0, 2) }}</h4>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm border-0 p-3 text-center bg-light">
                <h6 class="text-muted">Total Transactions</h6>
                <h4 class="fw-bold text-info">{{ $totalTransactions ?? 0 }}</h4>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm border-0 p-3 text-center bg-light">
                <h6 class="text-muted">Top Product (Units Sold)</h6>
                <h4 class="fw-bold text-warning">
                    {{ $topProducts->first()->product_name ?? 'N/A' }}
                </h4>
            </div>
        </div>

    </div>

    {{-- TOP PRODUCTS TABLE --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <strong>Top Selling Products</strong>
        </div>

        <div class="card-body p-0">
            <table class="table table-hover table-striped mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Product</th>
                        <th>Total Quantity Sold</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($topProducts as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->product_name }}</td>
                            <td>{{ $item->total_sold }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted">
                                No data available for selected range
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- SALES CHART --}}
    <div class="card shadow-sm">
        <div class="card-header bg-success text-white">
            <strong>Sales Chart (Daily)</strong>
        </div>
        <div class="card-body">
            <canvas id="salesChart" style="height: 300px;"></canvas>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('salesChart').getContext('2d');
    const labels = {!! json_encode($salesByDay->pluck('date')) !!};
    const data = {!! json_encode($salesByDay->pluck('total')) !!};

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Sales (₦)',
                data: data,
                borderColor: '#28a745',
                backgroundColor: 'rgba(40, 167, 69, 0.2)',
                fill: true,
                tension: 0.3,
                pointRadius: 4,
                pointBackgroundColor: '#28a745'
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: true },
                tooltip: { mode: 'index', intersect: false }
            },
            interaction: { mode: 'nearest', axis: 'x', intersect: false },
            scales: {
                y: { beginAtZero: true },
                x: { grid: { display: false } }
            }
        }
    });
</script>
@endsection
