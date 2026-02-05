<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PurchaseItem;
use Illuminate\Support\Facades\DB;

class SalesReportController extends Controller
{
    public function index(Request $request)
{
    $baseQuery = PurchaseItem::query();

    // Date range filter
    if ($request->filled('start_date') && $request->filled('end_date')) {
        $baseQuery->whereBetween('purchase_items.created_at', [
            $request->start_date . ' 00:00:00',
            $request->end_date . ' 23:59:59'
        ]);
    }

    // Shop filter
    if ($request->filled('shop_id')) {
        $baseQuery->where('purchase_items.shop_id', $request->shop_id);
    }

    // Clone queries for separate calculations
    $totalSalesQuery = clone $baseQuery;
    $totalTransactionsQuery = clone $baseQuery;
    $topProductsQuery = clone $baseQuery;
    $salesByDayQuery = clone $baseQuery;

    $data = [
        // TOP PRODUCTS with names
        'topProducts' => $topProductsQuery
            ->join('products', 'purchase_items.product_id', '=', 'products.id')
            ->select('products.name as product_name', DB::raw('SUM(purchase_items.quantity) as total_sold'))
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get(),

        // TOTAL TRANSACTIONS
        'totalTransactions' => $totalTransactionsQuery
            ->distinct()
            ->count('transaction_id'),

        // TOTAL SALES
        'totalSales' => $totalSalesQuery->sum('purchase_items.total_price'),

        // SALES BY DAY
        'salesByDay' => $salesByDayQuery
            ->select(
                DB::raw('DATE(purchase_items.created_at) as date'),
                DB::raw('SUM(purchase_items.total_price) as total')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get(),
    ];

    return view('admin.report.sales_report', $data);
}


}

