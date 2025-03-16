<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class UserController extends Controller
{
    use AuthorizesRequests;
    
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Użycie traita AuthorizesRequests
        $this->authorize('viewAny', User::class);
        
        $users = User::all();
        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Użycie traita AuthorizesRequests
        $this->authorize('create', User::class);
        
        return view('users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Użycie traita AuthorizesRequests
        $this->authorize('create', User::class);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string|in:admin,manager,investor,accountant,user',
        ]);
        
        $user = User::create($validated);
        
        return redirect()->route('users.index')
                         ->with('success', 'Użytkownik został utworzony pomyślnie.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        // Użycie traita AuthorizesRequests
        $this->authorize('view', $user);
        
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        // Użycie traita AuthorizesRequests
        $this->authorize('update', $user);
        
        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        // Użycie traita AuthorizesRequests
        $this->authorize('update', $user);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
            'role' => 'required|string|in:admin,manager,investor,accountant,user',
        ]);
        
        $user->update($validated);
        
        return redirect()->route('users.index')
                         ->with('success', 'Dane użytkownika zostały zaktualizowane.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        // Użycie traita AuthorizesRequests
        $this->authorize('delete', $user);
        
        $user->delete();
        
        return redirect()->route('users.index')
                         ->with('success', 'Użytkownik został usunięty.');
    }
}
