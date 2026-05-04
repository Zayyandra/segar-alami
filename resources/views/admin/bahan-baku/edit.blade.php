<x-layouts.admin title="Edit Bahan Baku" subtitle="Perbarui informasi bahan baku yang diperlukan untuk produksi Segar Alami.">
    <div class="max-w-3xl mx-auto space-y-5">

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm px-8 py-7">
            <form method="POST" action="{{ route('app.bahan-baku.update', $bahanBaku) }}">
                @method('PUT')
                @include('admin.bahan-baku._form', ['submitLabel' => 'Simpan Perubahan'])
            </form>
        </div>

    </div>
</x-layouts.admin>
