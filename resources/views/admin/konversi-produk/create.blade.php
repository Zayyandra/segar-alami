{{-- resources/views/admin/konversi-produk/create.blade.php --}}
<x-layouts.admin title="Tambah Konversi Produk" subtitle="Atur rasio kebutuhan bahan baku per satuan varian produk.">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm px-8 py-7">
            <form method="POST" action="{{ route('app.konversi-produk.store') }}">
                @include('admin.konversi-produk._form', ['submitLabel' => 'Simpan Konversi'])
            </form>
        </div>
    </div>
</x-layouts.admin>
