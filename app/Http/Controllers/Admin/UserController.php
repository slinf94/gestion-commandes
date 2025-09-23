<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::withTrashed()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    public function show(User $user)
    {
        $user->load(['orders', 'cartItems', 'notifications']);
        return view('admin.users.show', compact('user'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:100',
            'prenom' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'numero_telephone' => 'required|string|max:20|unique:users,numero_telephone',
            'ville' => 'required|string|max:100',
            'role' => 'required|in:client,admin,gestionnaire',
            'status' => 'required|in:pending,active,suspended,inactive',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'numero_telephone' => $request->numero_telephone,
            'ville' => $request->ville,
            'role' => $request->role,
            'status' => $request->status,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('admin.users.show', $user)
            ->with('success', 'Utilisateur créé avec succès');
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'nom' => 'required|string|max:100',
            'prenom' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'numero_telephone' => 'required|string|max:20|unique:users,numero_telephone,' . $user->id,
            'ville' => 'required|string|max:100',
            'role' => 'required|in:client,admin,gestionnaire',
            'status' => 'required|in:pending,active,suspended,inactive',
        ]);

        $user->update($request->only([
            'nom', 'prenom', 'email', 'numero_telephone', 'ville', 'role', 'status'
        ]));

        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        return redirect()->route('admin.users.show', $user)
            ->with('success', 'Utilisateur mis à jour avec succès');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.users.index')
            ->with('success', 'Utilisateur supprimé avec succès');
    }

    public function restore($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->restore();
        return redirect()->route('admin.users.index')
            ->with('success', 'Utilisateur restauré avec succès');
    }
}