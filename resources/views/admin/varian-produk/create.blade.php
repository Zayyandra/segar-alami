<x-layouts.admin title="Tambah Varian Produk" :subtitle="'Isi detail varian untuk produk yang dipilih.'">

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm px-8 py-7">
            <form method="POST" action="{{ route('app.varian-produk.store') }}">
                @include('admin.varian-produk._form', ['submitLabel' => 'Simpan Varian'])
            </form>
        </div>

    </div>
</x-layouts.admin>
