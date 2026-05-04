<x-layouts.admin :title="'Edit Produk'" :subtitle="'Perbarui informasi produk.'">
    <div class="max-w-2xl mx-auto">
        <x-admin.card>
            <form method="POST" action="{{ route('app.produk.update', $produk) }}"
                  enctype="multipart/form-data" class="p-6 sm:p-8">
                @csrf
                @method('PUT')
                @include('admin.produk._form', ['kategori' => $kategori, 'produk' => $produk])
            </form>
        </x-admin.card>
    </div>
</x-layouts.admin>
