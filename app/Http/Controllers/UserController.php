<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserController extends Controller
{
    // Display a list of users with pagination
    public function index()
    {
        $users = User::with('role')
            ->orderBy('created_at', 'desc') // Sort by most recent
            ->paginate(10);
        
        return Inertia::render('Admin/Users/UsersList', ['users' => $users]);
    }

    // Return a view to create a new user
    
    
    // Store a new user in the database
   
   
    
    // Display a specific user
   
    // Return a view to edit an existing user
    public function edit(User $user)
    {
        return Inertia::render('Admin/Users/UserEdit', ['user' => $user]);
    }

    // Update an existing user
    public function update(Request $request, User $user)
    {
        \Log::info($request->all()); // Same here

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8',
            'role_id' => 'required|exists:roles,id', // Validate role_id
        ]);

        $updateData = $request->only(['name', 'email', 'role_id']);
        if ($request->filled('password')) {
            $updateData['password'] = bcrypt($request->password);
        }

        $user->update($updateData);

        return redirect()->route('users.index');
    }

    // Delete a user
    
}
