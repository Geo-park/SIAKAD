<x-app-layout>
    <x-slot name="header">
        Periode Pembayaran
    </x-slot>

    @if(session('success'))
        <div class="mb-4 p-4 bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-300 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 rounded-xl flex items-center gap-3">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span class="text-sm font-medium">{{ session('success') }}</span>
        </div>
    @endif

    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-800 dark:text-white">Master Periode Pembayaran</h2>
            <p class="text-sm text-slate-500 dark:text-gray-400">Atur semester & rentang waktu pembayaran biaya kuliah mahasiswa.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.tagihan.index') }}" class="btn-ghost-saas px-4 py-2 rounded-xl text-sm font-medium">
                &larr; Kembali ke Tagihan
            </a>
            <button onclick="openModal('createPeriodModal')" class="btn-primary-saas px-4 py-2 rounded-xl text-sm font-semibold inline-flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                Tambah Periode
            </button>
        </div>
    </div>

    <div class="card-saas dark:bg-gray-800 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 dark:bg-gray-700/50 text-slate-600 dark:text-gray-300 font-semibold border-b border-slate-200 dark:border-gray-700">
                    <tr>
                        <th class="px-5 py-3.5">Nama Periode</th>
                        <th class="px-5 py-3.5">Tanggal Mulai</th>
                        <th class="px-5 py-3.5">Tanggal Berakhir</th>
                        <th class="px-5 py-3.5">Jumlah Tagihan</th>
                        <th class="px-5 py-3.5">Total Nominal</th>
                        <th class="px-5 py-3.5 text-center">Status Aktif</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-gray-700 text-slate-700 dark:text-gray-300">
                    @forelse($periods as $period)
                    <tr class="hover:bg-slate-50/70 dark:hover:bg-gray-700/40 transition">
                        <td class="px-5 py-4 font-semibold text-slate-900 dark:text-white">
                            {{ $period->name }}
                        </td>
                        <td class="px-5 py-4 text-slate-500">
                            {{ $period->start_date->format('d M Y') }}
                        </td>
                        <td class="px-5 py-4 text-slate-500">
                            {{ $period->end_date->format('d M Y') }}
                        </td>
                        <td class="px-5 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-sky-100 text-sky-800 dark:bg-sky-900/40 dark:text-sky-300">
                                {{ $period->bills_count }} invoice
                            </span>
                        </td>
                        <td class="px-5 py-4 font-semibold text-slate-800 dark:text-white">
                            Rp {{ number_format((float) ($period->total_bills_amount ?? 0), 0, ',', '.') }}
                        </td>
                        <td class="px-5 py-4 text-center">
                            @if($period->is_active)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300">
                                    Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-600 dark:bg-gray-700 dark:text-gray-400">
                                    Tidak Aktif
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-right">
                            <div class="inline-flex items-center gap-2 justify-end">
                                <form action="{{ route('admin.payment-periods.toggle', $period->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="text-xs px-2.5 py-1 rounded-lg border {{ $period->is_active ? 'border-amber-300 text-amber-700 hover:bg-amber-50' : 'border-emerald-300 text-emerald-700 hover:bg-emerald-50' }} transition">
                                        {{ $period->is_active ? 'Nonaktifkan' : 'Set Aktif' }}
                                    </button>
                                </form>
                                <button onclick="openEditModal({{ $period->id }}, '{{ addslashes($period->name) }}', '{{ $period->start_date->format('Y-m-d') }}', '{{ $period->end_date->format('Y-m-d') }}', {{ $period->is_active ? 'true' : 'false' }})" class="p-1.5 rounded-lg text-slate-500 hover:text-sky-600 hover:bg-sky-50 dark:hover:bg-gray-700">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-5 py-10 text-center text-slate-400">
                            Belum ada periode pembayaran. Tambahkan periode pertama untuk memulai penagihan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Create Period -->
    <div id="createPeriodModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm hidden">
        <div class="bg-white dark:bg-gray-800 rounded-2xl max-w-md w-full p-6 shadow-xl border border-slate-100 dark:border-gray-700 mx-4">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">Tambah Periode Pembayaran</h3>
            <form action="{{ route('admin.payment-periods.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-gray-300 uppercase mb-1">Nama Periode <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" required placeholder="Contoh: Semester Ganjil 2024/2025" class="input-saas w-full px-3 py-2 text-sm dark:bg-gray-900 dark:border-gray-700">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-gray-300 uppercase mb-1">Tanggal Mulai <span class="text-rose-500">*</span></label>
                    <input type="date" name="start_date" required class="input-saas w-full px-3 py-2 text-sm dark:bg-gray-900 dark:border-gray-700">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-gray-300 uppercase mb-1">Tanggal Berakhir <span class="text-rose-500">*</span></label>
                    <input type="date" name="end_date" required class="input-saas w-full px-3 py-2 text-sm dark:bg-gray-900 dark:border-gray-700">
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" id="is_active" name="is_active" value="1" class="rounded text-sky-600 focus:ring-sky-500" checked>
                    <label for="is_active" class="text-sm text-slate-700 dark:text-gray-300">Set sebagai periode pembayaran aktif</label>
                </div>
                <div class="flex justify-end gap-3 pt-3 border-t border-slate-100 dark:border-gray-700">
                    <button type="button" onclick="closeModal('createPeriodModal')" class="btn-ghost-saas px-4 py-2 rounded-xl text-sm font-medium">Batal</button>
                    <button type="submit" class="btn-primary-saas px-5 py-2 rounded-xl text-sm font-semibold">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Period -->
    <div id="editPeriodModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm hidden">
        <div class="bg-white dark:bg-gray-800 rounded-2xl max-w-md w-full p-6 shadow-xl border border-slate-100 dark:border-gray-700 mx-4">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">Edit Periode Pembayaran</h3>
            <form id="editPeriodForm" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-gray-300 uppercase mb-1">Nama Periode <span class="text-rose-500">*</span></label>
                    <input type="text" id="editName" name="name" required class="input-saas w-full px-3 py-2 text-sm dark:bg-gray-900 dark:border-gray-700">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-gray-300 uppercase mb-1">Tanggal Mulai <span class="text-rose-500">*</span></label>
                    <input type="date" id="editStartDate" name="start_date" required class="input-saas w-full px-3 py-2 text-sm dark:bg-gray-900 dark:border-gray-700">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-gray-300 uppercase mb-1">Tanggal Berakhir <span class="text-rose-500">*</span></label>
                    <input type="date" id="editEndDate" name="end_date" required class="input-saas w-full px-3 py-2 text-sm dark:bg-gray-900 dark:border-gray-700">
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" id="editIsActive" name="is_active" value="1" class="rounded text-sky-600 focus:ring-sky-500">
                    <label for="editIsActive" class="text-sm text-slate-700 dark:text-gray-300">Set sebagai periode pembayaran aktif</label>
                </div>
                <div class="flex justify-end gap-3 pt-3 border-t border-slate-100 dark:border-gray-700">
                    <button type="button" onclick="closeModal('editPeriodModal')" class="btn-ghost-saas px-4 py-2 rounded-xl text-sm font-medium">Batal</button>
                    <button type="submit" class="btn-primary-saas px-5 py-2 rounded-xl text-sm font-semibold">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
        function closeModal(id) { document.getElementById(id).classList.add('hidden'); }
        function openEditModal(id, name, start, end, active) {
            document.getElementById('editPeriodForm').action = `/admin/payment-periods/${id}`;
            document.getElementById('editName').value = name;
            document.getElementById('editStartDate').value = start;
            document.getElementById('editEndDate').value = end;
            document.getElementById('editIsActive').checked = active;
            openModal('editPeriodModal');
        }
    </script>
</x-app-layout>
