<x-layouts.admin :title="'Tambah Kategori'" :subtitle="'Buat kategori produk baru.'">
    <div class="max-w-2xl mx-auto">
        <x-admin.card>
            <form method="POST" action="{{ route('app.kategori.store') }}" class="p-6 sm:p-8">
                @csrf
                @include('admin.kategori._form')
            </form>
        </x-admin.card>
    </div>
</x-layouts.admin>
