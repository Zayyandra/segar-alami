<x-layouts.admin title="Laporan Masa Simpan">
    <div class="space-y-6">

        {{-- Header --}}
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Laporan Masa Simpan</h1>
            <p class="text-sm text-gray-500 mt-1">Pemantauan masa simpan bahan baku berdasarkan metode FEFO (First Expired First Out).</p>
        </div>

        {{-- Stat Cards --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <a href="{{ route('app.masa-simpan.index') }}"
               class="bg-white rounded-2xl p-4 shadow-sm border {{ !$status ? 'border-emerald-400 ring-1 ring-emerald-400' : 'border-gray-100' }}">
                <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Semua</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ $counts['semua'] }}</p>
            </a>
            <a href="{{ route('app.masa-simpan.index', ['status' => 'kadaluarsa']) }}"
               class="bg-white rounded-2xl p-4 shadow-sm border {{ $status === 'kadaluarsa' ? 'border-red-400 ring-1 ring-red-400' : 'border-gray-100' }}">
                <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Sudah Kedaluwarsa</p>
                <p class="text-2xl font-bold text-red-600 mt-1">{{ $counts['kadaluarsa'] }}</p>
            </a>
            <a href="{{ route('app.masa-simpan.index', ['status' => 'mendekati']) }}"
               class="bg-white rounded-2xl p-4 shadow-sm border {{ $status === 'mendekati' ? 'border-amber-400 ring-1 ring-amber-400' : 'border-gray-100' }}">
                <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Mendekati Kedaluwarsa</p>
                <p class="text-2xl font-bold text-amber-600 mt-1">{{ $counts['mendekati'] }}</p>
            </a>
            <a href="{{ route('app.masa-simpan.index', ['status' => 'aman']) }}"
               class="bg-white rounded-2xl p-4 shadow-sm border {{ $status === 'aman' ? 'border-emerald-400 ring-1 ring-emerald-400' : 'border-gray-100' }}">
                <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Aman</p>
                <p class="text-2xl font-bold text-emerald-600 mt-1">{{ $counts['aman'] }}</p>
            </a>
        </div>

        {{-- Tabel --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="font-semibold text-gray-800">Daftar Bahan Baku Masuk</h2>
                <p class="text-xs text-gray-400 mt-0.5">Diurutkan berdasarkan tanggal kedaluwarsa terdekat (FEFO)</p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 bg-gray-50">
                            <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Bahan Baku</th>
                            <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Jumlah</th>
                            <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Tanggal Masuk</th>
                            <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Tanggal Kedaluwarsa</th>
                            <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Sisa Hari</th>
                            <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse ($items as $item)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <p class="font-medium text-gray-900">{{ $item->bahanBaku->nama }}</p>
                                    <p class="text-xs text-gray-400">{{ $item->bahanBaku->satuan }}</p>
                                </td>
                                <td class="px-6 py-4 text-gray-700">
                                    {{ $item->jumlah }} {{ $item->bahanBaku->satuan }}
                                </td>
                                <td class="px-6 py-4 text-gray-600">
                                    {{ \Carbon\Carbon::parse($item->tanggal_masuk)->format('d M Y') }}
                                </td>
                                <td class="px-6 py-4 text-gray-600">
                                    {{ \Carbon\Carbon::parse($item->tanggal_kadaluarsa)->format('d M Y') }}
                                </td>
                                <td class="px-6 py-4 font-medium
                                    {{ $item->status === 'kadaluarsa' ? 'text-red-600' : ($item->status === 'mendekati' ? 'text-amber-600' : 'text-emerald-600') }}">
                                    {{ $item->sisa_hari }}
                                </td>
                                <td class="px-6 py-4">
                                    @if ($item->status === 'kadaluarsa')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">
                                            Sudah Kedaluwarsa
                                        </span>
                                    @elseif ($item->status === 'mendekati')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-700">
                                            Mendekati Kedaluwarsa
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">
                                            Aman
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                                    Tidak ada data bahan baku dengan filter ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.admin>
