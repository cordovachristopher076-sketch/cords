<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

class StudentController extends Controller
{
    /**
     * Display a listing of registered users.
     */
    public function index()
    {
        $students = User::where(function ($query) {
            $query->whereIn('role', ['student', 'faculty', 'staff', 'admin'])
                ->orWhereNull('role');
        })
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('admin.students.index', compact('students'));
    }

    /**
     * Store a new user account.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users'],
            'role' => ['required', 'string', 'in:admin,faculty,staff,student'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return redirect()->route('admin.students.index')->with('status', 'Student registered successfully.');
    }

    /**
     * Update an existing user account.
     */
    public function update(Request $request, User $student): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users')->ignore($student->id)],
            'role' => ['required', 'string', 'in:admin,faculty,staff,student'],
        ]);

        $student->update($validated);

        return redirect()->route('admin.students.index')->with('status', 'User updated successfully.');
    }

    /**
     * Delete a user account.
     */
    public function destroy(User $student): RedirectResponse
    {
        $student->delete();

        return redirect()->route('admin.students.index')->with('status', 'User deleted successfully.');
    }
}
