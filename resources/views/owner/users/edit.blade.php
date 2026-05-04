<x-layouts.admin title="Edit User">
    <div class="max-w-2xl mx-auto space-y-5">

        <div>
            <a href="{{ route('app.users.index') }}"
               class="inline-flex items-center gap-1 text-sm text-slate-500 hover:text-slate-700 transition">
                ← Kembali
            </a>
            <h1 class="mt-2 text-2xl font-semibold text-slate-900">Edit User</h1>
            <p class="mt-1 text-sm text-slate-500">Perbarui data akun {{ $user->name }}.</p>
        </div>

        @if ($errors->any())
            <div class="rounded-lg border border-red-100 bg-red-50 px-4 py-3 text-sm text-red-700">
                <p class="font-medium mb-1">Terdapat kesalahan:</p>
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm px-8 py-7">
            <form method="POST" action="{{ route('app.users.update', $user) }}" class="space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">
                        Nama Lengkap <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}"
                           class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm
                                  focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 focus:outline-none
                                  @error('name') border-red-400 @enderror">
                    @error('name')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">
                        Email <span class="text-red-500">*</span>
                    </label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}"
                           class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm
                                  focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 focus:outline-none
                                  @error('email') border-red-400 @enderror">
                    @error('email')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">
                        Role <span class="text-red-500">*</span>
                    </label>
                    <select name="role"
                            class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm bg-white
                                   focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 focus:outline-none">
                        @foreach ($roles as $role)
                            <option value="{{ $role->name }}"
                                @selected(old('role', $user->getRoleNames()->first()) === $role->name)>
                                {{ ucfirst($role->name) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="rounded-lg border border-slate-200 bg-slate-50 px-4 py-4">
                    <p class="text-xs font-semibold text-slate-600 uppercase tracking-wider mb-3">
                        Ganti Password <span class="font-normal text-slate-400">(kosongkan jika tidak ingin mengganti)</span>
                    </p>
                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Password Baru</label>
                            <input type="password" name="password"
                                   placeholder="Min. 8 karakter"
                                   class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm
                                          focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 focus:outline-none
                                          @error('password') border-red-400 @enderror">
                            @error('password')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation"
                                   placeholder="Ulangi password baru"
                                   class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm
                                          focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 focus:outline-none">
                        </div>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                    <a href="{{ route('app.users.index') }}"
                       class="px-4 py-2 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 transition">
                        Batal
                    </a>
                    <button type="submit"
                            class="px-5 py-2 rounded-lg bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-medium transition">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>

    </div>
</x-layouts.admin>
