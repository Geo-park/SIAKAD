<x-app-layout>
    <x-slot name="header">
        Detail Tagihan: {{ $bill->invoice_number }}
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

    <div class="mb-6 flex items-center justify-between">
        <a href="{{ route('admin.tagihan.index') }}" class="btn-ghost-saas px-4 py-2 rounded-xl text-sm font-medium inline-flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali ke Daftar Tagihan
        </a>
        <div class="flex items-center gap-2">
            <button onclick="window.print()" class="btn-ghost-saas px-3.5 py-2 rounded-xl text-sm font-medium border border-slate-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-slate-700 dark:text-gray-200 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Cetak Invoice
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Invoice & Payments Area -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Invoice Details Card -->
            <div class="card-saas p-6 dark:bg-gray-800">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-6 border-b border-slate-100 dark:border-gray-700 gap-4">
                    <div>
                        <span class="text-xs uppercase tracking-wider text-slate-400 font-semibold">Official University Invoice</span>
                        <h3 class="text-2xl font-black text-slate-900 dark:text-white font-mono mt-0.5">{{ $bill->invoice_number }}</h3>
                        <p class="text-sm text-slate-500 mt-1">{{ $bill->description ?: 'Biaya Pendidikan Mahasiswa' }}</p>
                    </div>
                    <div class="text-left sm:text-right">
                        @if($bill->status === 'paid')
                            <span class="inline-flex items-center px-3.5 py-1.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300">
                                <svg class="w-3.5 h-3.5 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                LUNAS (PAID)
                            </span>
                        @else
                            <span class="inline-flex items-center px-3.5 py-1.5 rounded-full text-xs font-bold bg-rose-100 text-rose-800 dark:bg-rose-900/40 dark:text-rose-300">
                                BELUM DIBAYAR (UNPAID)
                            </span>
                        @endif
                        <p class="text-xs text-slate-400 mt-2">Diterbitkan: {{ $bill->created_at->format('d M Y, H:i') }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 py-5 border-b border-slate-100 dark:border-gray-700 text-sm">
                    <div>
                        <p class="text-xs font-semibold text-slate-400 uppercase">Periode Akademik</p>
                        <p class="font-medium text-slate-800 dark:text-white mt-0.5">{{ $bill->paymentPeriod->name }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-slate-400 uppercase">Jatuh Tempo</p>
                        <p class="font-medium text-slate-800 dark:text-white mt-0.5">{{ $bill->due_date ? \Carbon\Carbon::parse($bill->due_date)->format('d F Y') : 'Tidak ditentukan' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-slate-400 uppercase">Virtual Account (VA)</p>
                        <p class="font-mono font-bold text-sky-700 dark:text-sky-400 text-base mt-0.5">{{ $bill->va_number }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-slate-400 uppercase">Bank Penerima</p>
                        <p class="font-medium text-slate-800 dark:text-white mt-0.5">Bank Mandiri / BNI / BRI (SIAKAD VA)</p>
                    </div>
                </div>

                <!-- Financial Breakdown -->
                <div class="pt-5 space-y-2 text-sm">
                    <div class="flex justify-between text-slate-600 dark:text-gray-300">
                        <span>Nominal Ketetapan Rektorat</span>
                        <span class="font-bold text-slate-900 dark:text-white text-base">Rp {{ number_format($bill->amount, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between pt-2 border-t border-slate-200 dark:border-gray-700 font-semibold text-sm">
                        <span class="text-slate-700 dark:text-gray-300">Status Pembayaran</span>
                        <span class="{{ $bill->status === 'paid' ? 'text-emerald-600' : 'text-rose-600 dark:text-rose-400' }}">
                            {{ $bill->status === 'paid' ? 'Lunas (Terbayar Penuh)' : 'Belum Dibayar' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Payment Audit Trail & History -->
            <div class="card-saas p-6 dark:bg-gray-800">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-base font-bold text-slate-900 dark:text-white">Riwayat Transaksi & Validasi Kunci Pembayaran</h4>
                    <span class="text-xs text-slate-400">{{ count($paymentsWithVerification) }} transaksi</span>
                </div>

                @if(count($paymentsWithVerification) > 0)
                <div class="space-y-4">
                    @foreach($paymentsWithVerification as $item)
                        @php $p = $item['payment']; $valid = $item['is_valid']; @endphp
                        <div class="p-4 rounded-xl border border-slate-200 dark:border-gray-700 bg-slate-50/50 dark:bg-gray-900/30">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 pb-3 border-b border-slate-200/60 dark:border-gray-700">
                                <div>
                                    <span class="font-mono font-bold text-slate-900 dark:text-white text-sm">{{ $p->reference_number }}</span>
                                    <span class="text-xs text-slate-400 block">{{ $p->payment_date->format('d M Y, H:i:s') }}</span>
                                </div>
                                <div class="text-left sm:text-right">
                                    <span class="text-base font-bold text-emerald-600 dark:text-emerald-400">Rp {{ number_format($p->amount_paid, 0, ',', '.') }}</span>
                                    <span class="text-xs text-slate-500 block">{{ $p->payment_method }}</span>
                                </div>
                            </div>
                            
                            <div class="mt-3 flex flex-col sm:flex-row sm:items-center justify-between text-xs gap-2">
                                <div class="text-slate-500">
                                    <span>Dicatat oleh: <strong>{{ $p->user->name ?? 'Mahasiswa Mandiri' }}</strong></span>
                                    @if($p->notes)
                                        <p class="text-slate-400 italic mt-0.5">Catatan: {{ $p->notes }}</p>
                                    @endif
                                </div>

                                <div>
                                    @if($valid)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300" title="HMAC SHA-256 Hash Valid: {{ $p->security_hash }}">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                            Integritas Sah (Hash Terverifikasi)
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-semibold bg-rose-100 text-rose-800 dark:bg-rose-900/40 dark:text-rose-300">
                                            Hash Tidak Cocok (Modifikasi Terdeteksi)
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-8 text-slate-400">
                    <p class="text-sm">Belum ada riwayat pembayaran yang tercatat pada tagihan ini.</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Sidebar / Right Side: Mahasiswa Info & Process Payment Form -->
        <div class="space-y-6">
            <!-- Mahasiswa Info Card -->
            <div class="card-saas p-6 dark:bg-gray-800">
                <h4 class="text-base font-bold text-slate-900 dark:text-white mb-4">Informasi Mahasiswa</h4>
                <div class="flex items-center gap-3 pb-4 border-b border-slate-100 dark:border-gray-700">
                    <div class="w-12 h-12 rounded-full bg-sky-100 dark:bg-sky-900 text-sky-700 dark:text-sky-300 flex items-center justify-center font-bold text-lg">
                        {{ strtoupper(substr($bill->mahasiswa->user->name ?? 'M', 0, 1)) }}
                    </div>
                    <div>
                        <p class="font-bold text-slate-900 dark:text-white">{{ $bill->mahasiswa->user->name }}</p>
                        <p class="text-xs text-slate-500 font-mono">NIM: {{ $bill->mahasiswa->nim }}</p>
                    </div>
                </div>

                <div class="py-4 space-y-3 text-xs border-b border-slate-100 dark:border-gray-700">
                    <div>
                        <span class="text-slate-400">Fakultas:</span>
                        <p class="font-semibold text-slate-700 dark:text-gray-200 mt-0.5">{{ $bill->mahasiswa->prodi->fakultas->nama ?? '-' }}</p>
                    </div>
                    <div>
                        <span class="text-slate-400">Program Studi:</span>
                        <p class="font-semibold text-slate-700 dark:text-gray-200 mt-0.5">{{ $bill->mahasiswa->prodi->nama ?? '-' }}</p>
                    </div>
                    <div>
                        <span class="text-slate-400">Tahun Angkatan:</span>
                        <p class="font-semibold text-slate-700 dark:text-gray-200 mt-0.5">{{ $bill->mahasiswa->angkatan ?? '-' }}</p>
                    </div>
                    <div>
                        <span class="text-slate-400">Status Akademik:</span>
                        <span class="inline-block mt-0.5 px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-emerald-100 text-emerald-800">{{ $bill->mahasiswa->status }}</span>
                    </div>
                </div>
            </div>

            <!-- Form Proses Pembayaran Baru -->
            @if($bill->status !== 'paid')
            <div class="card-saas p-6 dark:bg-gray-800 border-2 border-emerald-500/20">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-slate-900 dark:text-white">Input Pembayaran</h4>
                        <p class="text-[11px] text-slate-400">Nominal: Rp {{ number_format($bill->amount, 0, ',', '.') }}</p>
                    </div>
                </div>

                <form action="{{ route('admin.tagihan.process-payment', $bill->id) }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-gray-300 uppercase mb-1">Nominal (Rp) <span class="text-rose-500">*</span></label>
                        <input type="number" name="amount_paid" required readonly value="{{ $bill->amount }}" class="input-saas w-full px-3 py-2 text-sm dark:bg-gray-900 dark:border-gray-700 font-semibold bg-slate-100 dark:bg-gray-800 cursor-not-allowed">
                        <p class="text-[11px] text-slate-500 mt-1">Pembayaran harus sesuai ketetapan nominal Rektorat (tidak ada cicilan).</p>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-gray-300 uppercase mb-1">Metode Pembayaran <span class="text-rose-500">*</span></label>
                        <select name="payment_method" required class="input-saas w-full px-3 py-2 text-sm dark:bg-gray-900 dark:border-gray-700">
                            <option value="Transfer Bank - Virtual Account">Virtual Account</option>
                            <option value="Transfer Bank - Manual">Transfer Bank (Rekening)</option>
                            <option value="Setoran Tunai - Teller Kampus">Teller / Loket Kampus</option>
                            <option value="QRIS Online">QRIS Online</option>
                        </select>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="text-xs font-semibold text-slate-700 dark:text-gray-300 uppercase">Payment Key Unik <span class="text-rose-500">*</span></label>
                            <button type="button" onclick="regenerateKey()" class="text-xs text-sky-600 hover:text-sky-700 font-medium">Buat Baru</button>
                        </div>
                        <input type="text" id="inputPaymentKey" name="reference_number" required class="input-saas w-full px-3 py-2 font-mono text-xs dark:bg-gray-900 dark:border-gray-700 bg-slate-50" placeholder="PAY-20261011-XXXXXX">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-gray-300 uppercase mb-1">Catatan</label>
                        <input type="text" name="notes" placeholder="Nomor referensi / keterangan..." class="input-saas w-full px-3 py-2 text-sm dark:bg-gray-900 dark:border-gray-700">
                    </div>

                    <button type="submit" class="w-full btn-primary-saas py-2.5 rounded-xl text-sm font-semibold shadow-sm">
                        Proses Pembayaran
                    </button>
                </form>
            </div>
            @endif
        </div>
    </div>

    <script>
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

        function regenerateKey() {
            const el = document.getElementById('inputPaymentKey');
            if (el) el.value = generateRandomKey();
        }

        document.addEventListener('DOMContentLoaded', function() {
            regenerateKey();
        });
    </script>
</x-app-layout>
