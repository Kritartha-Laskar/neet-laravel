<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of users with role 5 and 7 (Admins & Super Admins).
     */
    public function index(Request $request)
    {
        $query = User::whereIn('role', [5, 7]);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('user_name', 'like', "%{$search}%")
                  ->orWhere('gmail', 'like', "%{$search}%")
                  ->orWhere('phone_no', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->latest()->paginate(15);

        return view('corse.user.index', compact('users'));
    }

    /**
     * Show form to manually create a user.
     */
    public function create()
    {
        if (Auth::check() && (int)Auth::user()->role !== 7) {
            abort(403, 'You are not authorized to create users.');
        }
        return view('corse.user.createUser');
    }

    /**
     * Store a manually created user.
     */
    public function store(Request $request)
    {
        if (Auth::check() && (int)Auth::user()->role !== 7) {
            abort(403, 'You are not authorized to create users.');
        }

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
            'user_type' => in_array($request->role, [5, 7]) ? 'admin' : 'user',
            'status'    => 'active',
            'role'      => $request->role,
        ]);

        return redirect()->route('admin.users.index')->with('success', 'User/Admin created successfully.');
    }

    /**
     * Show form to edit a user.
     */
    public function edit(User $user)
    {
        return view('corse.user.editUser', compact('user'));
    }

    /**
     * Update an existing user.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'      => 'required|string|max:191',
            'user_name' => 'required|string|max:191|unique:users,user_name,' . $user->id,
            'gmail'     => 'required|email|max:191|unique:users,gmail,' . $user->id,
            'phone_no'  => 'nullable|string|max:20|unique:users,phone_no,' . $user->id,
            'role'      => 'required|in:3,5,7',
            'status'    => 'required|in:active,inactive',
            'password'  => 'nullable|string|min:6|confirmed',
        ]);

        $userData = [
            'name'      => $request->name,
            'user_name' => $request->user_name,
            'gmail'     => $request->gmail,
            'phone_no'  => $request->phone_no,
            'role'      => $request->role,
            'user_type' => in_array($request->role, [5, 7]) ? 'admin' : 'user',
            'status'    => $request->status,
        ];

        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        $user->update($userData);

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    /**
     * Delete a user.
     */
    public function destroy(User $user)
    {
        if (Auth::id() === $user->id) {
            return redirect()->back()->with('error', 'You cannot delete your own account.');
        }

        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }
}
