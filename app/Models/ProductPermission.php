<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductPermission extends Model
{
    protected $fillable = [
        'manager_id',
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

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }
}