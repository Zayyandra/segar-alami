<x-layouts.admin title="Tambah Bahan Baku" subtitle="Catat bahan baku baru untuk produksi Segar Alami.">
    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm px-8 py-7">
            <form method="POST" action="{{ route('app.bahan-baku.store') }}">
                @include('admin.bahan-baku._form', ['submitLabel' => 'Simpan Bahan Baku'])
            </form>
        </div>
    </div>
</x-layouts.admin>
