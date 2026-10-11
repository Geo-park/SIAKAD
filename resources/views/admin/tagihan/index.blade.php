<x-app-layout>
    <x-slot name="header">
        Manajemen Tagihan & Pembayaran
    </x-slot>

    @if(session('success'))
        <div class="mb-4 p-4 bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-300 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 rounded-xl flex items-center gap-3">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span class="text-sm font-medium">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 p-4 bg-rose-50 dark:bg-rose-900/30 border border-rose-300 dark:border-rose-800 text-rose-800 dark:text-rose-300 rounded-xl flex items-center gap-3">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span class="text-sm font-medium">{{ session('error') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="mb-4 p-4 bg-rose-50 dark:bg-rose-900/30 border border-rose-300 dark:border-rose-800 text-rose-800 dark:text-rose-300 rounded-xl">
            @foreach($errors->all() as $err)
                <p class="text-sm">{{ $err }}</p>
            @endforeach
        </div>
    @endif

    <!-- Header & Action Bar -->
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-800 dark:text-white">Daftar Tagihan Mahasiswa</h2>
            <p class="text-sm text-slate-500 dark:text-gray-400">Monitoring keuangan, penerbitan invoice massal, dan verifikasi pembayaran anti-dobel.</p>
        </div>
        <div class="flex items-center gap-3 flex-wrap">
            <a href="{{ route('admin.payment-periods.index') }}" class="btn-ghost-saas px-4 py-2.5 rounded-xl text-sm font-semibold border border-slate-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:bg-slate-50 dark:hover:bg-gray-700 text-slate-700 dark:text-gray-200 flex items-center gap-2 transition">
                <svg class="w-4 h-4 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                Kelola Periode
            </a>
            <button onclick="openModal('massBillModal')" class="btn-primary-saas px-4 py-2.5 rounded-xl text-sm font-semibold inline-flex items-center gap-2 shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                Generate Tagihan Massal
            </button>
        </div>
    </div>

    <!-- Summary Metrics Cards (Full Payment Only) -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="card-saas p-5 dark:bg-gray-800 border-l-4 border-l-sky-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 dark:text-gray-400 uppercase tracking-wider">Total Tagihan</p>
                    <p class="text-2xl font-bold text-slate-800 dark:text-white mt-1">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</p>
                    <p class="text-xs text-slate-400 mt-1">{{ $totalBillsCount }} invoice diterbitkan</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-sky-50 dark:bg-sky-900/40 text-sky-600 dark:text-sky-400 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z"/></svg>
                </div>
            </div>
        </div>

        <div class="card-saas p-5 dark:bg-gray-800 border-l-4 border-l-emerald-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 dark:text-gray-400 uppercase tracking-wider">Total Terbayar (Lunas)</p>
                    <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400 mt-1">Rp {{ number_format($totalTerbayar, 0, ',', '.') }}</p>
                    <p class="text-xs text-slate-400 mt-1">{{ $paidBillsCount }} mahasiswa lunas</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-900/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>

        <div class="card-saas p-5 dark:bg-gray-800 border-l-4 border-l-rose-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 dark:text-gray-400 uppercase tracking-wider">Total Belum Bayar</p>
                    <p class="text-2xl font-bold text-rose-600 dark:text-rose-400 mt-1">Rp {{ number_format(max(0, $totalTagihan - $totalTerbayar), 0, ',', '.') }}</p>
                    <p class="text-xs text-slate-400 mt-1">{{ $unpaidBillsCount }} belum bayar</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-rose-50 dark:bg-rose-900/40 text-rose-600 dark:text-rose-400 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="card-saas p-4 mb-6 dark:bg-gray-800">
        <form method="GET" class="flex flex-col lg:flex-row items-center gap-3 w-full flex-wrap">
            <div class="w-full lg:w-64">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari NIM, Nama, Invoice..." class="input-saas px-4 py-2 text-sm w-full dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300">
            </div>

            <div class="w-full sm:w-48">
                <select name="payment_period_id" class="input-saas px-3 py-2 text-sm w-full dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300">
                    <option value="">Semua Periode</option>
                    @foreach($paymentPeriodsList as $p)
                        <option value="{{ $p->id }}" {{ request('payment_period_id') == $p->id ? 'selected' : '' }}>
                            {{ $p->name }} {{ $p->is_active ? '(Aktif)' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>

            @if(auth()->user()->role === 'rektorat' || !auth()->user()->fakultas_id)
            <div class="w-full sm:w-48">
                <select name="fakultas_id" id="filterFakultas" class="input-saas px-3 py-2 text-sm w-full dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300">
                    <option value="">Semua Fakultas</option>
                    @foreach($fakultasList as $f)
                        <option value="{{ $f->id }}" {{ request('fakultas_id') == $f->id ? 'selected' : '' }}>{{ $f->nama }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            <div class="w-full sm:w-48">
                <select name="prodi_id" id="filterProdi" class="input-saas px-3 py-2 text-sm w-full dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300">
                    <option value="">Semua Prodi</option>
                    @foreach($fakultasList as $f)
                        @foreach($f->prodi as $pr)
                            <option value="{{ $pr->id }}" data-fakultas-id="{{ $pr->fakultas_id }}" {{ request('prodi_id') == $pr->id ? 'selected' : '' }}>
                                {{ $pr->nama }}
                            </option>
                        @endforeach
                    @endforeach
                </select>
            </div>

            <div class="w-full sm:w-36">
                <select name="status" class="input-saas px-3 py-2 text-sm w-full dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300">
                    <option value="">Status Bayar</option>
                    <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Lunas (Paid)</option>
                    <option value="unpaid" {{ request('status') === 'unpaid' ? 'selected' : '' }}>Belum Bayar (Unpaid)</option>
                </select>
            </div>

            <div class="flex items-center gap-2 w-full lg:w-auto">
                <button type="submit" class="btn-primary-saas px-4 py-2 rounded-lg text-sm font-semibold">Terapkan</button>
                @if(request()->anyFilled(['search', 'payment_period_id', 'fakultas_id', 'prodi_id', 'status']))
                    <a href="{{ route('admin.tagihan.index') }}" class="btn-ghost-saas px-3 py-2 rounded-lg text-sm font-medium text-slate-500 hover:text-slate-700">Reset</a>
                @endif
            </div>
        </form>
    </div>

    <!-- Data Table -->
    <div class="card-saas dark:bg-gray-800 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 dark:bg-gray-700/50 text-slate-600 dark:text-gray-300 font-semibold border-b border-slate-200 dark:border-gray-700">
                    <tr>
                        <th class="px-5 py-3.5">Invoice & VA</th>
                        <th class="px-5 py-3.5">Mahasiswa</th>
                        <th class="px-5 py-3.5">Periode & Ket</th>
                        <th class="px-5 py-3.5">Nominal Tagihan</th>
                        <th class="px-5 py-3.5 text-center">Status</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-gray-700 text-slate-700 dark:text-gray-300">
                    @forelse($bills as $bill)
                    <tr class="hover:bg-slate-50/70 dark:hover:bg-gray-700/40 transition">
                        <td class="px-5 py-4">
                            <span class="font-mono font-bold text-sky-700 dark:text-sky-400 block">{{ $bill->invoice_number }}</span>
                            <span class="text-xs text-slate-400 dark:text-gray-400 font-mono">VA: {{ $bill->va_number }}</span>
                        </td>
                        <td class="px-5 py-4">
                            <p class="font-medium text-slate-900 dark:text-white">{{ $bill->mahasiswa->user->name ?? '-' }}</p>
                            <p class="text-xs text-slate-500 dark:text-gray-400">NIM: {{ $bill->mahasiswa->nim }} &bull; {{ $bill->mahasiswa->prodi->nama ?? '-' }}</p>
                        </td>
                        <td class="px-5 py-4">
                            <span class="font-medium">{{ $bill->paymentPeriod->name ?? '-' }}</span>
                            @if($bill->due_date)
                                <span class="block text-xs text-slate-400">Jatuh Tempo: {{ \Carbon\Carbon::parse($bill->due_date)->format('d M Y') }}</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 font-semibold text-slate-800 dark:text-white">
                            Rp {{ number_format($bill->amount, 0, ',', '.') }}
                        </td>
                        <td class="px-5 py-4 text-center">
                            @if($bill->status === 'paid')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                    Lunas
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-100 text-rose-800 dark:bg-rose-900/40 dark:text-rose-300">
                                    Belum Bayar
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-right">
                            <div class="inline-flex items-center gap-1.5 justify-end">
                                <a href="{{ route('admin.tagihan.show', $bill->id) }}" class="p-1.5 rounded-lg text-slate-500 hover:text-sky-600 hover:bg-sky-50 dark:hover:bg-gray-700 transition" title="Lihat Detail & Riwayat">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>

                                @if($bill->status !== 'paid')
                                <button onclick="openPaymentModal({{ $bill->id }}, '{{ $bill->invoice_number }}', '{{ addslashes($bill->mahasiswa->user->name ?? '') }}', {{ $bill->amount }})" class="p-1.5 rounded-lg text-emerald-600 hover:text-emerald-700 hover:bg-emerald-50 dark:hover:bg-gray-700 transition" title="Catat Pembayaran">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                </button>
                                @endif

                                @if($bill->payments->count() === 0)
                                <form action="{{ route('admin.tagihan.destroy', $bill->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus tagihan ini?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-gray-700 transition" title="Hapus Tagihan">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-5 py-12 text-center text-slate-400 dark:text-gray-500">
                            <svg class="w-12 h-12 mx-auto mb-3 text-slate-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z"/></svg>
                            <p class="font-medium text-base">Belum ada data tagihan</p>
                            <p class="text-xs mt-1">Gunakan tombol "Generate Tagihan Massal" untuk menerbitkan tagihan bagi mahasiswa.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($bills->hasPages())
        <div class="px-5 py-4 border-t border-slate-200 dark:border-gray-700">
            {{ $bills->links() }}
        </div>
        @endif
    </div>

    <!-- Modal Generate Tagihan Massal -->
    <div id="massBillModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm hidden">
        <div class="bg-white dark:bg-gray-800 rounded-2xl max-w-lg w-full p-6 shadow-xl border border-slate-100 dark:border-gray-700 mx-4 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-gray-700 mb-5">
                <div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">Penerbitan Tagihan Massal</h3>
                    <p class="text-xs text-slate-500">Otomatisasi pembuatan invoice untuk seluruh mahasiswa aktif.</p>
                </div>
                <button onclick="closeModal('massBillModal')" class="p-1 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form action="{{ route('admin.tagihan.store-mass') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-gray-300 uppercase mb-1">Periode Pembayaran <span class="text-rose-500">*</span></label>
                    <select name="payment_period_id" required class="input-saas w-full px-3 py-2 text-sm dark:bg-gray-900 dark:border-gray-700">
                        @foreach($paymentPeriodsList as $p)
                            <option value="{{ $p->id }}" {{ $p->is_active ? 'selected' : '' }}>
                                {{ $p->name }} {{ $p->is_active ? '(Aktif Saat Ini)' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-gray-300 uppercase mb-1">Besaran Nominal Tagihan (Rp) <span class="text-rose-500">*</span></label>
                    <input type="number" name="amount" required min="10000" step="50000" placeholder="Contoh: 3500000" value="3500000" class="input-saas w-full px-3 py-2 text-sm dark:bg-gray-900 dark:border-gray-700">
                    <p class="text-[11px] text-slate-400 mt-1">Nominal UKT / Biaya Kuliah yang akan ditagihkan ke mahasiswa.</p>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-gray-300 uppercase mb-1">Batas Waktu Pembayaran (Due Date)</label>
                    <input type="date" name="due_date" class="input-saas w-full px-3 py-2 text-sm dark:bg-gray-900 dark:border-gray-700">
                </div>

                @if(auth()->user()->role === 'rektorat' || !auth()->user()->fakultas_id)
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-gray-300 uppercase mb-1">Target Fakultas</label>
                        <select name="fakultas_id" id="massFakultas" class="input-saas w-full px-3 py-2 text-sm dark:bg-gray-900 dark:border-gray-700">
                            <option value="">Semua Fakultas</option>
                            @foreach($fakultasList as $f)
                                <option value="{{ $f->id }}">{{ $f->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-gray-300 uppercase mb-1">Target Program Studi</label>
                        <select name="prodi_id" id="massProdi" class="input-saas w-full px-3 py-2 text-sm dark:bg-gray-900 dark:border-gray-700">
                            <option value="">Semua Prodi</option>
                            @foreach($fakultasList as $f)
                                @foreach($f->prodi as $pr)
                                    <option value="{{ $pr->id }}" data-fakultas-id="{{ $pr->fakultas_id }}">{{ $pr->nama }}</option>
                                @endforeach
                            @endforeach
                        </select>
                    </div>
                </div>
                @endif

                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-gray-300 uppercase mb-1">Keterangan Tagihan</label>
                    <input type="text" name="description" placeholder="Contoh: UKT Semester Ganjil 2024/2025" class="input-saas w-full px-3 py-2 text-sm dark:bg-gray-900 dark:border-gray-700">
                </div>

                <div class="p-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl text-amber-800 dark:text-amber-300 text-xs">
                    <p class="font-semibold mb-0.5">Proteksi Duplikasi Otomatis:</p>
                    <p>Sistem memiliki constraint database unik yang menjamin mahasiswa yang sudah memiliki tagihan di periode terpilih tidak akan dibuatkan tagihan ganda.</p>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100 dark:border-gray-700">
                    <button type="button" onclick="closeModal('massBillModal')" class="btn-ghost-saas px-4 py-2 rounded-xl text-sm font-medium">Batal</button>
                    <button type="submit" class="btn-primary-saas px-5 py-2 rounded-xl text-sm font-semibold">Proses Penerbitan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Catat Pembayaran dengan Idempotency Key Validation -->
    <div id="paymentModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm hidden">
        <div class="bg-white dark:bg-gray-800 rounded-2xl max-w-lg w-full p-6 shadow-xl border border-slate-100 dark:border-gray-700 mx-4 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-gray-700 mb-5">
                <div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">Catat Pembayaran Tagihan</h3>
                    <p class="text-xs text-slate-500">Verifikasi pembayaran dengan sistem key transaksi unik.</p>
                </div>
                <button onclick="closeModal('paymentModal')" class="p-1 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form id="paymentForm" method="POST" class="space-y-4">
                @csrf
                <div class="p-3 bg-slate-50 dark:bg-gray-700/40 rounded-xl space-y-1">
                    <p class="text-xs text-slate-500">Invoice: <span id="modalInvoice" class="font-mono font-bold text-slate-800 dark:text-white"></span></p>
                    <p class="text-xs text-slate-500">Mahasiswa: <span id="modalStudent" class="font-semibold text-slate-800 dark:text-white"></span></p>
                    <p class="text-xs text-slate-500">Nominal Tagihan: <span id="modalRemaining" class="font-bold text-sky-600 dark:text-sky-400"></span></p>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-gray-300 uppercase mb-1">Nominal Pembayaran (Rp) <span class="text-rose-500">*</span></label>
                    <input type="number" id="modalAmountInput" name="amount_paid" required readonly class="input-saas w-full px-3 py-2 text-sm dark:bg-gray-900 dark:border-gray-700 font-semibold bg-slate-100 dark:bg-gray-800 cursor-not-allowed">
                    <p class="text-[11px] text-slate-500 mt-1">Sesuai ketetapan Rektorat, pembayaran wajib lunas penuh (tidak ada sistem cicilan).</p>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-gray-300 uppercase mb-1">Metode Pembayaran <span class="text-rose-500">*</span></label>
                    <select name="payment_method" required class="input-saas w-full px-3 py-2 text-sm dark:bg-gray-900 dark:border-gray-700">
                        <option value="Transfer Bank - Virtual Account">Transfer Bank (Virtual Account)</option>
                        <option value="Transfer Bank - Manual">Transfer Bank (Rekening Kampus)</option>
                        <option value="Setoran Tunai - Teller Kampus">Setoran Tunai (Teller / Loket Kampus)</option>
                        <option value="QRIS Online">QRIS Online</option>
                    </select>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="text-xs font-semibold text-slate-700 dark:text-gray-300 uppercase">Payment Key Unik (Idempotency Key) <span class="text-rose-500">*</span></label>
                        <button type="button" onclick="regeneratePaymentKey()" class="text-xs text-sky-600 hover:text-sky-700 font-medium">Buat Key Baru</button>
                    </div>
                    <input type="text" id="modalPaymentKey" name="reference_number" required class="input-saas w-full px-3 py-2 font-mono text-sm dark:bg-gray-900 dark:border-gray-700 bg-slate-50" placeholder="PAY-20261011-XXXXXX">
                    <p class="text-[11px] text-slate-400 mt-1">Setiap transaksi WAJIB memiliki key unik. Jika key ini sudah pernah diproses, sistem otomatis menolaknya.</p>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-gray-300 uppercase mb-1">Catatan / Bukti Ref</label>
                    <input type="text" name="notes" placeholder="Nomor resi bank atau catatan petugas..." class="input-saas w-full px-3 py-2 text-sm dark:bg-gray-900 dark:border-gray-700">
                </div>

                <!-- Skema Pengamanan Callout -->
                <div class="p-3 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-xl text-emerald-800 dark:text-emerald-300 text-xs">
                    <div class="flex items-start gap-2">
                        <svg class="w-4 h-4 text-emerald-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        <div>
                            <p class="font-bold">Skema Pengamanan Transaksi Aktif:</p>
                            <p>Pessimistic concurrency lock + Database unique key + HMAC SHA-256 hash menjamin tidak ada double-payment.</p>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100 dark:border-gray-700">
                    <button type="button" onclick="closeModal('paymentModal')" class="btn-ghost-saas px-4 py-2 rounded-xl text-sm font-medium">Batal</button>
                    <button type="submit" class="btn-primary-saas px-5 py-2 rounded-xl text-sm font-semibold">Proses & Kunci Pembayaran</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Scripts for Modals & Dynamic Cascading Filters -->
    <script>
        function openModal(id) {
            document.getElementById(id).classList.remove('hidden');
        }

        function closeModal(id) {
            document.getElementById(id).classList.add('hidden');
        }

        function generateRandomKey() {
            const now = new Date();
            const dateStr = now.getFullYear().toString() +
                String(now.getMonth() + 1).padStart(2, '0') +
                String(now.getDate()).padStart(2, '0') +
                String(now.getHours()).padStart(2, '0') +
                String(now.getMinutes()).padStart(2, '0') +
                String(now.getSeconds()).padStart(2, '0');
            const randomSuffix = Math.random().toString(36).substring(2, 8).toUpperCase();
            return `PAY-${dateStr}-${randomSuffix}`;
        }

        function regeneratePaymentKey() {
            document.getElementById('modalPaymentKey').value = generateRandomKey();
        }

        function openPaymentModal(billId, invoiceNumber, studentName, amount) {
            document.getElementById('paymentForm').action = `/admin/tagihan/${billId}/bayar`;
            document.getElementById('modalInvoice').textContent = invoiceNumber;
            document.getElementById('modalStudent').textContent = studentName;
            document.getElementById('modalRemaining').textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(amount);
            document.getElementById('modalAmountInput').value = amount;
            document.getElementById('modalPaymentKey').value = generateRandomKey();
            openModal('paymentModal');
        }

        // Dynamic cascade for filters & mass modal
        document.addEventListener('DOMContentLoaded', function() {
            function handleCascade(fakultasSelectId, prodiSelectId) {
                const fSelect = document.getElementById(fakultasSelectId);
                const pSelect = document.getElementById(prodiSelectId);
                if (!fSelect || !pSelect) return;

                fSelect.addEventListener('change', function() {
                    const selFakultas = this.value;
                    const options = pSelect.querySelectorAll('option');
                    let firstVisible = false;

                    options.forEach(opt => {
                        if (!opt.value) return; // "Semua Prodi"
                        const fId = opt.getAttribute('data-fakultas-id');
                        if (!selFakultas || fId === selFakultas) {
                            opt.style.display = '';
                            if (!firstVisible) firstVisible = true;
                        } else {
                            opt.style.display = 'none';
                            if (opt.selected) opt.selected = false;
                        }
                    });
                });
            }

            handleCascade('filterFakultas', 'filterProdi');
            handleCascade('massFakultas', 'massProdi');
        });
    </script>
</x-app-layout>
