<x-layouts.admin title="Catat Bahan Masuk" subtitle="Catat bahan baku yang baru diterima dari supplier.">
    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm px-8 py-7">
            <form method="POST" action="{{ route('app.bahan-masuk.store') }}">
                @include('admin.bahan-masuk._form', ['submitLabel' => 'Simpan Bahan Masuk'])
            </form>
        </div>
    </div>
</x-layouts.admin>
