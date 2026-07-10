<x-layouts.admin title="Riwayat Koreksi Stok">
    <div class="max-w-4xl mx-auto space-y-5">

        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-bold text-slate-900">{{ $bahanBaku->nama }}</h2>
                <p class="text-sm text-slate-500">Riwayat koreksi stok manual</p>
            </div>
            <a href="{{ route('app.koreksi-stok.create', $bahanBaku) }}"
                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-amber-500 hover:bg-amber-600 text-white text-sm font-medium transition">
                + Koreksi Stok
            </a>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <table class="w-full">
                <thead class="bg-slate-50">
                    <tr class="text-left text-xs uppercase tracking-wide text-slate-500">
                        <th class="px-6 py-3 font-medium">Tanggal</th>
                        <th class="px-6 py-3 font-medium text-right">Sebelum</th>
                        <th class="px-6 py-3 font-medium text-right">Sesudah</th>
                        <th class="px-6 py-3 font-medium text-right">Selisih</th>
                        <th class="px-6 py-3 font-medium">Alasan</th>
                        <th class="px-6 py-3 font-medium">Oleh</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($riwayat as $r)
                        <tr>
                            <td class="px-6 py-4 text-sm text-slate-500">{{ $r->created_at->format('d M Y H:i') }}</td>
                            <td class="px-6 py-4 text-right text-sm text-slate-600">{{ number_format($r->stok_sebelum, 2) }}</td>
                            <td class="px-6 py-4 text-right text-sm font-semibold text-slate-900">{{ number_format($r->stok_sesudah, 2) }}</td>
                            <td class="px-6 py-4 text-right text-sm font-semibold {{ $r->selisih >= 0 ? 'text-emerald-600' : 'text-red-500' }}">
                                {{ $r->selisih >= 0 ? '+' : '' }}{{ number_format($r->selisih, 2) }}
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600">
                                {{ \App\Models\KoreksiStok::ALASAN_OPTIONS[$r->alasan] ?? $r->alasan }}
                                @if ($r->keterangan)
                                    <p class="text-xs text-slate-400 mt-0.5">{{ $r->keterangan }}</p>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-500">{{ $r->user->name ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-sm text-slate-400">Belum ada koreksi stok.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            @if ($riwayat->hasPages())
                <div class="px-6 py-4 border-t border-slate-100">{{ $riwayat->links() }}</div>
            @endif
        </div>
    </div>
</x-layouts.admin>
