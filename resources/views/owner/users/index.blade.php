<x-layouts.admin title="User Management" subtitle="Kelola akun pengguna sistem Segar Alami.">

    <x-admin.card>
        <x-slot:title>Daftar User</x-slot:title>
        <x-slot:action>
            <x-admin.button :href="route('app.users.create')">+ Tambah User</x-admin.button>
        </x-slot:action>

        <div class="px-6 py-4 border-b border-slate-100">
            <form method="GET" action="{{ route('app.users.index') }}" class="flex gap-3">
                <div class="relative flex-1">
                    <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z"/>
                    </svg>
                    <input type="search" name="q" value="{{ request('q') }}"
                           placeholder="Cari nama atau email..."
                           class="w-full pl-10 px-3 py-2 rounded-lg border border-slate-200 text-sm
                                  focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 focus:outline-none">
                </div>
                @if (request()->anyFilled(['q']))
                    <a href="{{ route('app.users.index') }}"
                       class="px-4 py-2 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 transition">
                        Reset
                    </a>
                @endif
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50">
                    <tr class="text-left text-xs uppercase tracking-wide text-slate-500">
                        <th class="px-6 py-3 font-medium">Nama</th>
                        <th class="px-6 py-3 font-medium">Email</th>
                        <th class="px-6 py-3 font-medium">Role</th>
                        <th class="px-6 py-3 font-medium">Bergabung</th>
                        <th class="px-6 py-3 font-medium text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($users as $user)
                        <tr class="hover:bg-slate-50/50">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center
                                                text-emerald-700 text-sm font-semibold shrink-0">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <span class="text-sm font-medium text-slate-900">{{ $user->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600">{{ $user->email }}</td>
                            <td class="px-6 py-4">
                                @foreach ($user->roles as $role)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium
                                                 {{ $role->name === 'owner' ? 'bg-purple-50 text-purple-700' : 'bg-emerald-50 text-emerald-700' }}">
                                        {{ ucfirst($role->name) }}
                                    </span>
                                @endforeach
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-500">{{ $user->created_at->format('d M Y') }}</td>
                            <td class="px-6 py-4 text-right">
                                <div class="inline-flex items-center gap-3">
                                    <a href="{{ route('app.users.edit', $user) }}"
                                       class="text-sm font-medium text-emerald-600 hover:text-emerald-700">Edit</a>
                                    @if ($user->id !== auth()->id())
                                        <form method="POST" action="{{ route('app.users.destroy', $user) }}"
                                              class="inline" data-confirm="Hapus user {{ $user->name }}?">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                    class="text-sm font-medium text-red-600 hover:text-red-700">Hapus</button>
                                        </form>
                                    @else
                                        <span class="text-xs text-slate-400">Akun aktif</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-sm text-slate-400">
                                Belum ada user.
                                <a href="{{ route('app.users.create') }}" class="text-emerald-600 hover:underline">Tambahkan sekarang.</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($users->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">{{ $users->links() }}</div>
        @endif
    </x-admin.card>

</x-layouts.admin>
