<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('roles')->where('id', '!=', 1)->get();
        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string',
            'password' => 'required|string|min:8',
            'role' => 'required|in:agent,secretary',
            'is_active' => 'boolean',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['is_active'] = $request->has('is_active');
        
        $user = User::create($validated);
        $user->assignRole($validated['role']);

        return redirect()->route('users.index')->with('success', 'Utilisateur créé !');
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string',
            'password' => 'nullable|string|min:8',
            'is_active' => 'boolean',
        ]);

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $validated['is_active'] = $request->has('is_active');
        $user->update($validated);

        return redirect()->route('users.index')->with('success', 'Utilisateur mis à jour !');
    }

    public function destroy(User $user)
    {
        if ($user->hasRole('super_admin')) {
            return back()->with('error', 'Impossible de supprimer un super admin !');
        }

        $user->delete();
        return redirect()->route('users.index')->with('success', 'Utilisateur supprimé !');
    }
}
