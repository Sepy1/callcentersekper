<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $this->authorizeAdmin();

        $query = User::query();
        if ($request->filled('q')) {
            $search = $request->input('q');
            $query->where(function ($sub) use ($search) {
                $sub->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('no_hp', 'like', "%{$search}%")
                    ->orWhere('kode_kantor', 'like', "%{$search}%");
            });
        }
        if ($request->filled('role')) {
            $query->where('role', $request->input('role'));
        }

        return view('admin.users.index', [
            'users' => $query->orderBy('name')->paginate(10)->withQueryString(),
        ]);
    }

    public function store(Request $request)
    {
        $this->authorizeAdmin();
        $attributes = $request->validate($this->rules());
        $attributes['password'] = Hash::make($attributes['password']);
        $attributes['kode_kantor'] = $attributes['role'] === 'cabang' ? $attributes['kode_kantor'] : null;
        User::create($attributes);

        return redirect()->route('admin.users')->with('success', 'User berhasil dibuat.');
    }

    public function update(Request $request, User $user)
    {
        $this->authorizeAdmin();
        $attributes = $request->validate($this->rules($user));

        if ($user->is(auth()->user()) && $attributes['role'] !== 'admin') {
            return back()->withErrors(['role' => 'Role akun admin yang sedang digunakan tidak dapat diubah.'])->withInput();
        }

        if (!empty($attributes['password'])) {
            $attributes['password'] = Hash::make($attributes['password']);
        } else {
            unset($attributes['password']);
        }
        $attributes['kode_kantor'] = $attributes['role'] === 'cabang' ? $attributes['kode_kantor'] : null;

        $user->update($attributes);

        return redirect()->route('admin.users')->with('success', 'Informasi user berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        $this->authorizeAdmin();
        abort_if($user->is(auth()->user()), 422, 'Akun yang sedang digunakan tidak dapat dihapus.');

        DB::transaction(function () use ($user) {
            DB::table('activity_logs')->where('user_id', $user->id)->update(['user_id' => null]);
            $user->delete();
        });

        return redirect()->route('admin.users')->with('success', 'User berhasil dihapus.');
    }

    private function rules(?User $user = null): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user?->id)],
            'no_hp' => ['nullable', 'string', 'max:20', 'regex:/^[0-9+() .-]+$/'],
            'role' => ['required', Rule::in(['admin', 'officer', 'qa', 'cabang'])],
            'kode_kantor' => ['nullable', 'required_if:role,cabang', 'string', 'max:50'],
            'password' => [$user ? 'nullable' : 'required', 'string', 'min:8', 'confirmed'],
        ];
    }

    private function authorizeAdmin(): void
    {
        abort_unless(auth()->check() && auth()->user()->role === 'admin', 403);
    }
}
