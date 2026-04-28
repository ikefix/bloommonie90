<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'owner_id', // 🔥 IMPORTANT
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

    /*
    |-------------------------------------------------
    | RELATIONSHIPS
    |-------------------------------------------------
    */

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Fetch products by category
     */
    public function getProductsByCategory($categoryId)
    {
        return Product::where('category_id', $categoryId)->get();
    }

    public function stockTransfers()
    {
        return $this->hasMany(StockTransfer::class, 'to_shop_id');
    }

}
