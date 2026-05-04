<x-layouts.admin title="Edit Bahan Masuk" subtitle="Perbarui data bahan masuk yang sudah dicatat.">
    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm px-8 py-7">
            <form method="POST" action="{{ route('app.bahan-masuk.update', $bahanMasuk) }}">
                @method('PUT')
                @include('admin.bahan-masuk._form', ['submitLabel' => 'Simpan Perubahan'])
            </form>
        </div>
    </div>
</x-layouts.admin>
