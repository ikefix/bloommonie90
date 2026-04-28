<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Shop;
use Illuminate\Support\Facades\Auth;

class RoleController extends Controller

{
    
public function index()
{
    if (Auth::user()->role !== 'admin') {
        abort(403, 'Unauthorized action.');
    }

    $ownerId = auth()->user()->owner_id ?? auth()->id();

    $users = User::with('shop')
        ->whereIn('role', ['manager', 'cashier'])
        ->where('owner_id', $ownerId) // 🔒 THIS IS THE FIX
        ->get();

    $shops = Shop::where('owner_id', $ownerId)->get(); // 🔒 ALSO FIX THIS

    return view('admin.manage_roles', compact('users', 'shops'));
}

    public function updateRole(Request $request, $id)
    {
        if (Auth::user()->role !== 'admin') {
            return abort(403, 'Unauthorized action.');
        }

        $user = User::findOrFail($id);
        $user->role = $request->role;
        $user->save();

        return redirect()->back()->with('success', 'User role updated successfully.');
    }
}

