<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Complaint;
use Illuminate\Support\Facades\Mail;
use App\Mail\ComplaintMail;

class ComplaintController extends Controller
{

public function create()
{
    $user = auth()->user();

    if ($user->role === 'admin') {
        $layout = 'layouts.adminapp';
        $section = 'admincontent';
    } elseif ($user->role === 'manager') {
        $layout = 'layouts.managerapp';
        $section = 'managercontent';
    } else {
        $layout = 'layouts.app';
        $section = 'content';
    }

    return view('complaints.create', compact('layout', 'section'));
}

public function store(Request $request)
{
    $request->validate([
        'message' => 'required',
        'image' => 'nullable|image|max:2048'
    ]);

    $imagePath = null;

    if ($request->hasFile('image')) {
        $imagePath = $request->file('image')
                             ->store('complaints', 'public');
    }

    $complaint = Complaint::create([
        'user_id' => auth()->id(),
        'message' => $request->message,
        'image' => $imagePath
    ]);

    Mail::to('willsharches@gmail.com')
        ->send(new ComplaintMail($complaint));

    return back()->with('success', 'Complaint submitted');
}
}
