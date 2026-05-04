<x-layouts.admin :title="'Tambah Produk'" :subtitle="'Tambahkan produk baru ke katalog.'">
    <div class="max-w-2xl mx-auto">
        <x-admin.card>
            <form method="POST" action="{{ route('app.produk.store') }}" enctype="multipart/form-data" class="p-6 sm:p-8">
                @csrf
                @include('admin.produk._form', ['kategori' => $kategori])
            </form>
        </x-admin.card>
    </div>
</x-layouts.admin>
