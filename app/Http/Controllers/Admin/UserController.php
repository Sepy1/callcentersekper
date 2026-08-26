<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function update(Request $request, User $user)
    {
        abort_unless(auth()->user()->role === 'admin', 403);

        $attributes = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'no_hp' => ['nullable', 'string', 'max:20', 'regex:/^[0-9+() .-]+$/'],
            'role' => ['required', Rule::in(['admin', 'officer', 'qa'])],
        ]);

        $user->update($attributes);

        return redirect()->route('admin.users')->with('success', 'Informasi user berhasil diperbarui.');
    }
}
