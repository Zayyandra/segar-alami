<x-layouts.admin title="Edit Bahan Keluar" subtitle="Perbarui data pemakaian bahan baku.">
    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm px-8 py-7">
            <form method="POST" action="{{ route('app.bahan-keluar.update', $bahanKeluar) }}">
                @method('PUT')
                @include('admin.bahan-keluar._form', ['submitLabel' => 'Simpan Perubahan'])
            </form>
        </div>
    </div>
</x-layouts.admin>
