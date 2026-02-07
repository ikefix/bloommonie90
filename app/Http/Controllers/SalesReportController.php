<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PurchaseItem;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Shop;

class SalesReportController extends Controller
{
public function index(Request $request)
{
    $shops = Shop::orderBy('name')->get();

    // Base query for cards & tables
    $baseQuery = PurchaseItem::query();

    // Base query for chart
    $chartQuery = PurchaseItem::query();

    // DATE FILTER
    if ($request->filled('start_date') && $request->filled('end_date')) {

        $start = Carbon::parse($request->start_date)->startOfDay();
        $end   = Carbon::parse($request->end_date)->endOfDay();

        $baseQuery->whereBetween('purchase_items.created_at', [$start, $end]);
        $chartQuery->whereBetween('purchase_items.created_at', [$start, $end]);

        $chartStart = $start->copy();
        $chartEnd   = $end->copy();

    } else {
        // Cards = Today
        $baseQuery->whereDate('purchase_items.created_at', Carbon::today());

        // Chart = Last 7 days
        $chartStart = Carbon::today()->subDays(6)->startOfDay();
        $chartEnd   = Carbon::today()->endOfDay();

        $chartQuery->whereBetween('purchase_items.created_at', [$chartStart, $chartEnd]);
    }

    // SHOP FILTER
    if ($request->filled('shop_id')) {
        $baseQuery->where('purchase_items.shop_id', $request->shop_id);
        $chartQuery->where('purchase_items.shop_id', $request->shop_id);
    }

    /**
     * =========================
     * CHART DATA (WITH ZERO DAYS)
     * =========================
     */

    // Get existing sales from DB
    $dbSales = $chartQuery
        ->select(
            DB::raw('DATE(purchase_items.created_at) as date'),
            DB::raw('SUM(purchase_items.total_price) as total')
        )
        ->groupBy('date')
        ->pluck('total', 'date');

    // Force date range (7 days or selected range)
    $salesByDay = collect();

    $daysDiff = $chartStart->diffInDays($chartEnd);

    for ($i = 0; $i <= $daysDiff; $i++) {
        $date = $chartStart->copy()->addDays($i)->toDateString();

        $salesByDay->push([
            'date'  => $date,
            'total' => $dbSales[$date] ?? 0
        ]);
    }

    return view('admin.report.sales_report', [
        'shops' => $shops,

        // Cards
        'totalSales' => (clone $baseQuery)->sum('purchase_items.total_price'),

        'totalTransactions' => (clone $baseQuery)
            ->distinct()
            ->count('transaction_id'),

        'topProducts' => (clone $baseQuery)
            ->join('products', 'purchase_items.product_id', '=', 'products.id')
            ->select(
                'products.name as product_name',
                DB::raw('SUM(purchase_items.quantity) as total_sold')
            )
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get(),

        // Chart (ZERO-SALES DAYS INCLUDED ✅)
        'salesByDay' => $salesByDay,
    ]);
}




}

