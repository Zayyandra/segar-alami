<x-layouts.admin :title="'Edit Kategori'" :subtitle="'Perbarui informasi kategori.'">
    <div class="max-w-2xl mx-auto">
        <x-admin.card>
            <form method="POST" action="{{ route('app.kategori.update', $kategori) }}" class="p-6 sm:p-8">
                @csrf
                @method('PUT')
                @include('admin.kategori._form', ['kategori' => $kategori])
            </form>
        </x-admin.card>
    </div>
</x-layouts.admin>
