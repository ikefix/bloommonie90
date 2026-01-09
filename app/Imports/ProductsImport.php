<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\Category;
use App\Models\Shop;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductsImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Trim and normalize values
            $productName = trim($row['name']);
            $categoryName = trim($row['category']);
            $shopName = trim($row['shop']);

            // Lookup category ID by name
            $category = Category::where('name', $categoryName)->first();
            if (!$category) {
                // Skip or create category if it doesn't exist
                // $category = Category::create(['name' => $categoryName]);
                continue;
            }

            // Lookup shop ID by name
            $shop = Shop::where('name', $shopName)->first();
            if (!$shop) {
                // Skip or create shop if it doesn't exist
                // $shop = Shop::create(['name' => $shopName]);
                continue;
            }

            // Use updateOrCreate to add new or update existing product
            Product::updateOrCreate(
                [
                    'name' => $productName,
                    'category_id' => $category->id,
                    'shop_id' => $shop->id,
                ],
                [
                    'price' => $row['price'] ?? 0,
                    'cost_price' => $row['cost_price'] ?? 0,
                    'stock_quantity' => $row['stock_quantity'] ?? 0,
                    'stock_limit' => $row['stock_limit'] ?? 0,
                    'barcode' => $row['barcode'] ?? null,
                    'description' => $row['description'] ?? null,
                ]
            );
        }
    }
}
