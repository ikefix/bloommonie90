@extends('layouts.adminapp')

@section('admincontent')
<div class="container-fluid">

    {{-- PAGE TITLE --}}
    <div class="mb-4">
        <h3 class="fw-bold text-primary">Stock / Inventory Report</h3>
        <p class="text-muted">Track moving, sleeping and low stock items</p>
    </div>

    {{-- FILTERS --}}
    <form method="GET" class="row g-3 mb-4 align-items-end">

        {{-- SEARCH BAR --}}
        <div class="col-md-4">
            <label class="form-label">Search Product</label>
            <input
                type="text"
                name="search"
                value=""
                class="form-control"
                placeholder="Search by product name..."
                id="searchInput"
                autofocus
            >
        </div>

        {{-- SHOP FILTER --}}
        <div class="col-md-4">
            <label class="form-label">Shop</label>
            <select name="shop_id" class="form-select">
                <option value="">All Shops</option>
                @foreach($shops as $shop)
                    <option value="{{ $shop->id }}" {{ request('shop_id') == $shop->id ? 'selected' : '' }}>
                        {{ $shop->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- APPLY BUTTON --}}
        <div class="col-md-2 d-grid">
            <button class="btn btn-primary">Apply</button>
        </div>
    </form>

    {{-- SUMMARY CARDS + CHART INLINE --}}
    <div class="row mb-4 g-3 align-items-stretch">

    {{-- TOTAL PRODUCTS --}}
    <div class="col-md-4">
        <div class="card shadow-sm p-3 bg-light h-100 d-flex flex-column justify-content-center text-center">
            <div class="mb-2">
                <i class="bi bi-box-seam text-primary" style="font-size: 2rem;"></i>
            </div>
            <h6 class="text-muted">Total Products</h6>
            <h3 class="fw-bold">{{ $totalProducts }}</h3>
            <p class="text-muted mb-0">All products in inventory</p>
        </div>
    </div>

    {{-- LOW STOCK --}}
    <div class="col-md-4">
        <div class="card shadow-sm p-3 bg-light h-100 d-flex flex-column justify-content-center text-center">
            <div class="mb-2">
                <i class="bi bi-exclamation-triangle-fill text-danger" style="font-size: 2rem;"></i>
            </div>
            <h6 class="text-muted">Low Stock Items</h6>
            <h3 class="fw-bold text-danger">{{ $lowStockCount }}</h3>
            <p class="text-muted mb-0">Products below stock limit</p>
        </div>
    </div>

    {{-- CHART --}}
    <div class="col-md-4">
        <div class="card shadow-sm p-3 bg-light h-100 d-flex flex-column justify-content-center text-center">
            <div class="mb-2">
                <i class="bi bi-graph-up text-success" style="font-size: 2rem;"></i>
            </div>
            <h6 class="text-muted mb-2">Stock Health Overview</h6>
            <canvas id="stockChart" height="100"></canvas>
        </div>
    </div>

</div>


    {{-- STOCK TABLE --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <strong>Stock Breakdown</strong>
        </div>
        <div class="card-body p-0">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Shop</th>
                        <th>Opening Stock</th>
                        <th>Stock Added</th>
                        <th>Stock Sold</th>
                        <th>Remaining</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        <tr>
                            <td>{{ $product->name }}</td>
                            <td>{{ $product->shop->name ?? '-' }}</td>
                            <td>{{ $product->opening_stock }}</td>
                            <td>{{ $product->stock_added }}</td>
                            <td>{{ $product->stock_sold }}</td>
                            <td>{{ $product->remaining_stock }}</td>
                            <td>
                                @if($product->remaining_stock <= $product->stock_limit)
                                    <span class="badge bg-danger">Low</span>
                                @else
                                    <span class="badge bg-success">OK</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No products found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- PAGINATION --}}
    <div class="mt-3 d-flex justify-content-center">
        {{ $products->links('pagination::bootstrap-5') }}
    </div>

</div>

{{-- CHART.JS --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('stockChart');
new Chart(ctx, {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($stockChart['labels']) !!},
        datasets: [{
            data: {!! json_encode($stockChart['data']) !!},
            backgroundColor: ['#dc3545', '#28a745']
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'bottom' } }
    }
});
</script>

{{-- SEARCH BAR JS: ACTIVE SEARCH, CURSOR STAYS, NO PAGE RELOAD UNTIL SUBMIT --}}
<script>
const searchInput = document.getElementById('searchInput');

if (searchInput) {
    searchInput.focus(); // always focused

    // Keep cursor at end
    // searchInput.addEventListener('keydown', function () {
    //     const val = this.value;
    //     setTimeout(() => {
    //         this.selectionStart = this.selectionEnd = val.length;
    //     }, 0);
    // });

    window.addEventListener('pageshow', function(event) {
        if (event.persisted || (window.performance && window.performance.navigation.type === 1)) {
            searchInput.value = '';
        }
    });

    // searchInput.focus();

    // Keep cursor at end while typing
    searchInput.addEventListener('input', function () {
        const val = this.value;
        setTimeout(() => { this.selectionStart = this.selectionEnd = val.length; }, 0);
    });

    // Debounce typing auto-submit
    let timer;
    searchInput.addEventListener('keyup', function(e) {
        clearTimeout(timer);
        timer = setTimeout(() => {
            if (this.value.trim() !== '') {
                this.form.submit();
            }
        }, 500);
    });

    // Submit on Enter key
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            this.form.submit();
        }
    });
}
</script>

@endsection
