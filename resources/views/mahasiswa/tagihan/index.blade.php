<x-app-layout>
    <x-slot name="header">
        Tagihan & Keuangan Mahasiswa
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

    <!-- Active Semester Hero Banner -->
    @if($activeBill)
    <div class="mb-8 rounded-2xl overflow-hidden border {{ $activeBill->status === 'paid' ? 'bg-gradient-to-br from-emerald-600 via-teal-700 to-slate-900 border-emerald-500/30' : 'bg-gradient-to-br from-slate-900 via-sky-950 to-indigo-950 border-sky-500/30' }} text-white p-6 sm:p-8 shadow-lg relative">
        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div class="space-y-3 max-w-xl">
                <div class="flex items-center gap-2">
                    <span class="px-3 py-1 rounded-full text-xs font-bold tracking-wide uppercase {{ $activeBill->status === 'paid' ? 'bg-emerald-400/20 text-emerald-200 border border-emerald-400/30' : 'bg-amber-400/20 text-amber-200 border border-amber-400/30' }}">
                        {{ $activeBill->paymentPeriod->name ?? 'Semester Aktif' }}
                    </span>
                    @if($activeBill->status === 'paid')
                        <span class="inline-flex items-center gap-1 text-xs font-semibold text-emerald-300">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            Tagihan Lunas
                        </span>
                    @else
                        <span class="text-xs text-rose-300 font-semibold">&bull; Menunggu Pembayaran</span>
                    @endif
                </div>

                <h3 class="text-2xl sm:text-3xl font-extrabold tracking-tight">
                    {{ $activeBill->description ?: 'Biaya Kuliah (UKT) Semester Aktif' }}
                </h3>

                <!-- VA Code & Copy Button -->
                <div class="pt-2">
                    <p class="text-xs text-sky-200/80 mb-1">Nomor Virtual Account (VA) Pembayaran Anda:</p>
                    <div class="inline-flex items-center gap-3 bg-white/10 backdrop-blur-md px-4 py-2.5 rounded-xl border border-white/20">
                        <span id="vaCode" class="font-mono text-xl sm:text-2xl font-black tracking-wider text-sky-200">{{ $activeBill->va_number }}</span>
                        <button onclick="copyToClipboard('vaCode')" class="p-1.5 rounded-lg bg-white/20 hover:bg-white/30 text-white transition text-xs font-semibold flex items-center gap-1.5" title="Salin Nomor VA">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/></svg>
                            <span id="copyBtnText">Salin</span>
                        </button>
                    </div>
                </div>
            </div>

            <div class="bg-white/10 backdrop-blur-md p-5 rounded-2xl border border-white/20 min-w-[280px] space-y-3">
                <div>
                    <span class="text-xs text-sky-200">Nominal Tagihan (Ketetapan Rektorat):</span>
                    <p class="text-2xl font-black text-white">Rp {{ number_format($activeBill->amount, 0, ',', '.') }}</p>
                </div>
                <div class="pt-2 border-t border-white/10">
                    <span class="text-xs text-slate-300">Status Pembayaran:</span>
                    <p class="text-lg font-bold {{ $activeBill->status === 'paid' ? 'text-emerald-300' : 'text-amber-300' }}">
                        {{ $activeBill->status === 'paid' ? 'LUNAS' : 'BELUM DIBAYAR' }}
                    </p>
                </div>
                @if($activeBill->due_date)
                <p class="text-[11px] text-slate-300">
                    Batas Waktu: {{ \Carbon\Carbon::parse($activeBill->due_date)->format('d F Y') }}
                </p>
                @endif
                <div class="pt-2">
                    <a href="{{ route('mahasiswa.tagihan.show', $activeBill->id) }}" class="w-full inline-flex items-center justify-center py-2.5 px-4 rounded-xl text-sm font-bold bg-white text-slate-900 hover:bg-sky-50 transition shadow-md">
                        {{ $activeBill->status !== 'paid' ? 'Bayar Sekarang' : 'Lihat Kwitansi Resmi' }} &rarr;
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endif


    <!-- Table Riwayat Semua Tagihan -->
    <div class="card-saas dark:bg-gray-800 overflow-hidden shadow-sm">
        <div class="p-5 border-b border-slate-100 dark:border-gray-700 flex items-center justify-between">
            <h3 class="text-base font-bold text-slate-900 dark:text-white">Riwayat Tagihan Seluruh Semester</h3>
            <span class="text-xs text-slate-400 font-mono">{{ $bills->count() }} Invoice</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 dark:bg-gray-700/50 text-slate-600 dark:text-gray-300 font-semibold border-b border-slate-200 dark:border-gray-700">
                    <tr>
                        <th class="px-5 py-3.5">Invoice</th>
                        <th class="px-5 py-3.5">Periode Akademik</th>
                        <th class="px-5 py-3.5">Keterangan</th>
                        <th class="px-5 py-3.5">Nominal Tagihan</th>
                        <th class="px-5 py-3.5 text-center">Status</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-gray-700 text-slate-700 dark:text-gray-300">
                    @forelse($bills as $bill)
                    <tr class="hover:bg-slate-50/70 dark:hover:bg-gray-700/40 transition">
                        <td class="px-5 py-4 font-mono font-bold text-sky-700 dark:text-sky-400">
                            {{ $bill->invoice_number }}
                        </td>
                        <td class="px-5 py-4 font-medium">
                            {{ $bill->paymentPeriod->name }}
                        </td>
                        <td class="px-5 py-4 text-xs text-slate-500">
                            {{ $bill->description }}
                        </td>
                        <td class="px-5 py-4 font-semibold text-slate-900 dark:text-white">
                            Rp {{ number_format($bill->amount, 0, ',', '.') }}
                        </td>
                        <td class="px-5 py-4 text-center">
                            @if($bill->status === 'paid')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300">
                                    Lunas
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-100 text-rose-800 dark:bg-rose-900/40 dark:text-rose-300">
                                    Belum Lunas
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-right">
                            <a href="{{ route('mahasiswa.tagihan.show', $bill->id) }}" class="btn-ghost-saas px-3 py-1.5 rounded-lg text-xs font-semibold border border-slate-200 dark:border-gray-700 inline-flex items-center gap-1">
                                Detail & Bayar &rarr;
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-5 py-10 text-center text-slate-400">
                            Belum ada tagihan kuliah yang diterbitkan untuk akun Anda.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function copyToClipboard(elementId) {
            const text = document.getElementById(elementId).innerText;
            navigator.clipboard.writeText(text).then(() => {
                const btn = document.getElementById('copyBtnText');
                if (btn) {
                    btn.innerText = 'Tersalin!';
                    setTimeout(() => { btn.innerText = 'Salin'; }, 2000);
                }
            });
        }
    </script>
</x-app-layout>
