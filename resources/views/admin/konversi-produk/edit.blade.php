{{-- resources/views/admin/konversi-produk/edit.blade.php --}}
<x-layouts.admin title="Edit Konversi Produk" subtitle="Ubah rasio kebutuhan bahan baku per satuan varian produk.">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm px-8 py-7">
            <form method="POST" action="{{ route('app.konversi-produk.update', $konversiProduk) }}">
                @method('PUT')
                @include('admin.konversi-produk._form', ['submitLabel' => 'Perbarui Konversi'])
            </form>
        </div>
    </div>
</x-layouts.admin>
