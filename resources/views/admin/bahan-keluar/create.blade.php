<x-layouts.admin title="Catat Bahan Keluar" subtitle="Catat pemakaian bahan baku untuk produksi.">
    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm px-8 py-7">
            <form method="POST" action="{{ route('app.bahan-keluar.store') }}">
                @include('admin.bahan-keluar._form', ['submitLabel' => 'Simpan Bahan Keluar'])
            </form>
        </div>
    </div>
</x-layouts.admin>
