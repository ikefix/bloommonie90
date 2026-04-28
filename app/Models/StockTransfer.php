<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockTransfer extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'shop_id',
        'to_shop_id',
        'quantity',
        'cost_price',
        'selling_price',
        'owner_id', // 🔥 ONLY ADDITION
    ];

    /*
    |-------------------------------------------------
    | TENANT ISOLATION
    |-------------------------------------------------
    */
    protected static function booted()
    {
        static::addGlobalScope('owner', function ($query) {
            $user = auth()->user();

            if ($user && $user->role !== 'superadmin') {
                $ownerId = $user->owner_id ?? $user->id;
                $query->where('owner_id', $ownerId);
            }
        });

        static::creating(function ($model) {
            $user = auth()->user();

            if ($user) {
                $model->owner_id = $user->owner_id ?? $user->id;
            }
        });
    }

    // Product being transferred
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // From shop
    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    // To shop
    public function toShop()
    {
        return $this->belongsTo(Shop::class, 'to_shop_id');
    }

    // Category
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}