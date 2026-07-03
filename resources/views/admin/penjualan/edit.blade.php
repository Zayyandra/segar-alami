<x-layouts.admin title="Edit Transaksi">
    <div class="max-w-4xl mx-auto space-y-5">


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

        <form method="POST" action="{{ route('app.penjualan.update', $penjualan) }}" id="form-penjualan">
            @csrf
            @method('PUT')

            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm px-8 py-6 mb-5">
                <h2 class="text-sm font-semibold text-slate-700 uppercase tracking-wider mb-4">Informasi Transaksi</h2>
                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">
                            Tanggal <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="tanggal"
                            value="{{ old('tanggal', $penjualan->tanggal->format('Y-m-d')) }}"
                            class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm
                                      focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">
                            Keterangan <span class="text-xs text-slate-400 font-normal">(opsional)</span>
                        </label>
                        <input type="text" name="keterangan" value="{{ old('keterangan', $penjualan->keterangan) }}"
                            placeholder="Contoh: Penjualan pagi hari"
                            class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm
                                      focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 focus:outline-none">
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm px-8 py-6 mb-5">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-sm font-semibold text-slate-700 uppercase tracking-wider">Item Penjualan</h2>
                    <button type="button" id="btn-tambah-item"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-emerald-300
                                   text-emerald-700 text-sm font-medium hover:bg-emerald-50 transition">
                        + Tambah Item
                    </button>
                </div>

                <div class="overflow-x-auto -mx-2">
                    <table class="w-full min-w-[640px]">
                        <thead>
                            <tr
                                class="text-left text-xs uppercase tracking-wide text-slate-400 border-b border-slate-100">
                                <th class="px-2 py-2 font-medium w-[40%]">Produk / Varian</th>
                                <th class="px-2 py-2 font-medium w-[15%]">Jumlah</th>
                                <th class="px-2 py-2 font-medium w-[20%]">Harga Satuan</th>
                                <th class="px-2 py-2 font-medium w-[20%] text-right">Subtotal</th>
                                <th class="px-2 py-2 w-[5%]"></th>
                            </tr>
                        </thead>
                        <tbody id="item-container">
                            @foreach ($penjualan->details as $i => $detail)
                                <tr class="item-row border-b border-slate-50">
                                    <td class="px-2 py-3">
                                        <select name="items[{{ $i }}][varian_produk_id]"
                                            class="varian-select w-full rounded-lg border border-slate-200 px-3 py-2 text-sm bg-white
                                                       focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 focus:outline-none">
                                            <option value="">— Pilih varian —</option>
                                            @foreach ($variants as $v)
                                                <option value="{{ $v->id }}" data-harga="{{ $v->harga }}"
                                                    @selected($detail->varian_produk_id == $v->id)>
                                                    {{ $v->produk->nama }} —
                                                    {{ $v->nama_varian }}{{ $v->ukuran ? ' (' . $v->ukuran . ')' : '' }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="px-2 py-3">
                                        <input type="number" name="items[{{ $i }}][jumlah]"
                                            value="{{ $detail->jumlah }}" min="1"
                                            class="jumlah-input w-full rounded-lg border border-slate-200 px-3 py-2 text-sm
                                                      focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 focus:outline-none">
                                    </td>
                                    <td class="px-2 py-3">
                                        <input type="number" name="items[{{ $i }}][harga_satuan]"
                                            value="{{ $detail->harga_satuan }}" min="0" step="1"
                                            class="harga-input w-full rounded-lg border border-slate-200 px-3 py-2 text-sm
                                                      focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 focus:outline-none">
                                    </td>
                                    <td class="px-2 py-3 text-right">
                                        <span class="subtotal-display text-sm font-medium text-slate-700">
                                            Rp {{ number_format($detail->sub_total, 0, ',', '.') }}
                                        </span>
                                    </td>
                                    <td class="px-2 py-3 text-center">
                                        <button type="button"
                                            class="btn-hapus-item text-slate-300 hover:text-red-500 transition text-lg leading-none">×</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-5 pt-4 border-t border-slate-100 flex items-center justify-end gap-4">
                    <span class="text-sm font-medium text-slate-600">Total Transaksi</span>
                    <span id="grand-total" class="text-xl font-bold text-slate-900">
                        Rp {{ number_format($penjualan->total, 0, ',', '.') }}
                    </span>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('app.penjualan.show', $penjualan) }}"
                    class="px-4 py-2 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 transition">
                    Batal
                </a>
                <button type="submit"
                    class="px-6 py-2.5 rounded-lg bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-medium transition">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

    <script>
        let rowIndex = {{ $penjualan->details->count() }};

        const variantOptions = `
            <option value="">— Pilih varian —</option>
            @foreach ($variants as $v)
                <option value="{{ $v->id }}" data-harga="{{ $v->harga }}">{{ $v->produk->nama }} — {{ $v->nama_varian }}{{ $v->ukuran ? ' (' . $v->ukuran . ')' : '' }}</option>
            @endforeach
        `;

        function formatRupiah(number) {
            return 'Rp ' + Math.round(number).toLocaleString('id-ID');
        }

        function hitungSubtotal(row) {
            const jumlah = parseFloat(row.querySelector('.jumlah-input').value) || 0;
            const harga = parseFloat(row.querySelector('.harga-input').value) || 0;
            row.querySelector('.subtotal-display').textContent = formatRupiah(jumlah * harga);
            hitungTotal();
        }

        function hitungTotal() {
            let total = 0;
            document.querySelectorAll('.item-row').forEach(row => {
                const jumlah = parseFloat(row.querySelector('.jumlah-input').value) || 0;
                const harga = parseFloat(row.querySelector('.harga-input').value) || 0;
                total += jumlah * harga;
            });
            document.getElementById('grand-total').textContent = formatRupiah(total);
        }

        function attachRowEvents(row) {
            row.querySelector('.varian-select').addEventListener('change', function() {
                const harga = this.options[this.selectedIndex].dataset.harga || '';
                row.querySelector('.harga-input').value = harga;
                hitungSubtotal(row);
            });
            row.querySelector('.jumlah-input').addEventListener('input', () => hitungSubtotal(row));
            row.querySelector('.harga-input').addEventListener('input', () => hitungSubtotal(row));
            row.querySelector('.btn-hapus-item').addEventListener('click', function() {
                if (document.querySelectorAll('.item-row').length <= 1) return;
                row.remove();
                reindexRows();
                hitungTotal();
            });
        }

        function reindexRows() {
            document.querySelectorAll('.item-row').forEach((row, i) => {
                row.querySelector('.varian-select').name = `items[${i}][varian_produk_id]`;
                row.querySelector('.jumlah-input').name = `items[${i}][jumlah]`;
                row.querySelector('.harga-input').name = `items[${i}][harga_satuan]`;
            });
        }

        document.querySelectorAll('.item-row').forEach(row => attachRowEvents(row));

        document.getElementById('btn-tambah-item').addEventListener('click', function() {
            const tbody = document.getElementById('item-container');
            const tr = document.createElement('tr');
            tr.className = 'item-row border-b border-slate-50';
            tr.innerHTML = `
                <td class="px-2 py-3">
                    <select name="items[${rowIndex}][varian_produk_id]"
                            class="varian-select w-full rounded-lg border border-slate-200 px-3 py-2 text-sm bg-white
                                   focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 focus:outline-none">
                        ${variantOptions}
                    </select>
                </td>
                <td class="px-2 py-3">
                    <input type="number" name="items[${rowIndex}][jumlah]" value="1" min="1"
                           class="jumlah-input w-full rounded-lg border border-slate-200 px-3 py-2 text-sm
                                  focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 focus:outline-none">
                </td>
                <td class="px-2 py-3">
                    <input type="number" name="items[${rowIndex}][harga_satuan]" value="" min="0" step="1" placeholder="0"
                           class="harga-input w-full rounded-lg border border-slate-200 px-3 py-2 text-sm
                                  focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 focus:outline-none">
                </td>
                <td class="px-2 py-3 text-right">
                    <span class="subtotal-display text-sm font-medium text-slate-700">Rp 0</span>
                </td>
                <td class="px-2 py-3 text-center">
                    <button type="button" class="btn-hapus-item text-slate-300 hover:text-red-500 transition text-lg leading-none">×</button>
                </td>
            `;
            tbody.appendChild(tr);
            attachRowEvents(tr);
            rowIndex++;
        });
    </script>
</x-layouts.admin>
