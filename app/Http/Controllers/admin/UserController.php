<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Show form to manually create a user.
     */
    public function create()
    {
        return view('corse.user.createUser');
    }

    /**
     * Store a manually created user.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:191',
            'user_name' => 'required|string|max:191|unique:users,user_name',
            'gmail'     => 'required|email|max:191|unique:users,gmail',
            'phone_no'  => 'nullable|string|max:20|unique:users,phone_no',
            'role'      => 'required|in:3,5,7',
            'password'  => 'required|string|min:6|confirmed',
        ]);

        User::create([
            'name'      => $request->name,
            'user_name' => $request->user_name,
            'gmail'     => $request->gmail,
            'phone_no'  => $request->phone_no,
            'password'  => Hash::make($request->password),
            'user_type' => $request->role == 7 ? 'admin' : ($request->role == 5 ? 'admin' : 'user'),
            'status'    => 'active',
            'role'      => $request->role,
        ]);

        return redirect()->route('dashboard')->with('success', 'User/Admin created successfully.');
    }
}
