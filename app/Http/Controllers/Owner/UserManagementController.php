<?php
namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class UserManagementController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::with('roles')->latest('id');

        if ($search = trim((string) $request->input('q'))) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->paginate(15)->withQueryString();
        $roles = Role::all();

        return view('owner.users.index', compact('users', 'roles'));
    }

    public function create(): View
    {
        $roles = Role::all();
        return view('owner.users.create', compact('roles'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:100'],
            'email'    => ['required', 'email', 'unique:users,email'],
            'password' => ['required', Password::min(8)->letters()->numbers(), 'confirmed'],
            'role'     => ['required', 'exists:roles,name'],
        ], [
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole($request->role);

        return redirect()
            ->route('app.users.index')
            ->with('success', 'User berhasil ditambahkan.');
    }

    public function edit(User $user): View
    {
        $roles = Role::all();
        return view('owner.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'name'  => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', "unique:users,email,{$user->id}"],
            'password' => ['nullable', Password::min(8)->letters()->numbers(), 'confirmed'],
            'role'     => ['required', 'exists:roles,name'],
        ], [
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $user->update([
            'name'  => $request->name,
            'email' => $request->email,
            ...(filled($request->password)
                    ? ['password' => Hash::make($request->password)]
                    : []),
        ]);

        $user->syncRoles([$request->role]);

        return redirect()
            ->route('app.users.index')
            ->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(User $user): RedirectResponse
    {
        // Cegah owner hapus diri sendiri
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak bisa menghapus akun sendiri.');
        }

        // User dengan riwayat aktivitas tidak boleh dihapus (jaga integritas data)
        $punyaRiwayat = \App\Models\Penjualan::where('user_id', $user->id)->exists()
        || \App\Models\BahanMasuk::where('user_id', $user->id)->exists()
        || \App\Models\BahanKeluar::where('user_id', $user->id)->exists();

        if ($punyaRiwayat) {
            return back()->with('error', 'User tidak dapat dihapus karena memiliki riwayat transaksi. Data historis harus tetap terjaga.');
        }

        $user->delete();

        return redirect()
            ->route('app.users.index')
            ->with('success', 'User berhasil dihapus.');
    }
}
