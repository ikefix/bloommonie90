<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockReportController extends Controller
{
    public function index(Request $request)
    {
        // Get all shops for filter
        $shops = Shop::orderBy('name')->get();

        // Query for table (search + shop filter)
        $tableQuery = Product::with('shop');

        if ($request->filled('shop_id')) {
            $tableQuery->where('shop_id', $request->shop_id);
        }

        if ($request->filled('search')) {
            $tableQuery->where('name', 'like', '%' . $request->search . '%');
        }

        // Paginate filtered products
        $products = $tableQuery
            ->select(
                'products.id',
                'products.name',
                'products.shop_id',
                'products.stock_quantity',
                'products.stock_limit',
                DB::raw('products.stock_quantity as opening_stock'),
                DB::raw('0 as stock_added'),
                DB::raw('0 as stock_sold'),
                DB::raw('products.stock_quantity as remaining_stock')
            )
            ->orderBy('products.name')
            ->paginate(20)
            ->withQueryString(); // Keep search/shop in URL

        // Stats for ALL products (chart + cards)
        $statsQuery = Product::query();
        $totalProducts = $statsQuery->count();
        $lowStockCount = $statsQuery->whereColumn('stock_quantity', '<=', 'stock_limit')->count();

        $stockChart = [
            'labels' => ['Low Stock', 'Normal Stock'],
            'data'   => [$lowStockCount, max($totalProducts - $lowStockCount, 0)],
        ];

        return view('admin.report.stock_report', compact(
            'shops', 'products', 'totalProducts', 'lowStockCount', 'stockChart'
        ));
    }
}
