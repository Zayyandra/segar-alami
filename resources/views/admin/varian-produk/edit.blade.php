<x-layouts.admin title="Edit Varian Produk">
    <div class="max-w-3xl mx-auto space-y-5">

        <div>
            <a href="{{ route('app.varian-produk.index') }}"
               class="inline-flex items-center gap-1 text-sm text-slate-500 hover:text-slate-700 transition">
                ← Kembali
            </a>
            <h1 class="mt-2 text-2xl font-semibold text-slate-900">Edit Varian Produk</h1>
            <p class="mt-1 text-sm text-slate-500">
                {{ $varian->produk->nama }}
                <span class="mx-1 text-slate-300">·</span>
                {{ $varian->nama_varian }}
                @if ($varian->ukuran)
                    <span class="mx-1 text-slate-300">·</span>{{ $varian->ukuran }}
                @endif
            </p>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm px-8 py-7">
            <form method="POST" action="{{ route('app.varian-produk.update', $varian) }}">
                @method('PUT')
                @include('admin.varian-produk._form', ['submitLabel' => 'Simpan Perubahan'])
            </form>
        </div>

    </div>
</x-layouts.admin>
