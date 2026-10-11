<x-app-layout>
    <x-slot name="header">
        Detail Invoice & Pembayaran
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
        <a href="{{ route('mahasiswa.tagihan.index') }}" class="btn-ghost-saas px-4 py-2 rounded-xl text-sm font-medium inline-flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali ke Tagihan Saya
        </a>
        <button onclick="window.print()" class="btn-ghost-saas px-3.5 py-2 rounded-xl text-sm font-medium border border-slate-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-slate-700 dark:text-gray-200 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            Cetak Kwitansi
        </button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Area: Invoice Statement -->
        <div class="lg:col-span-2 space-y-6">
            <div class="card-saas p-6 sm:p-8 dark:bg-gray-800">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-6 border-b border-slate-100 dark:border-gray-700 gap-4">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="w-2.5 h-2.5 rounded-full bg-sky-500"></span>
                            <span class="text-xs uppercase tracking-wider text-slate-400 font-bold">Kwitansi Tagihan Mahasiswa</span>
                        </div>
                        <h2 class="text-2xl font-black text-slate-900 dark:text-white font-mono">{{ $bill->invoice_number }}</h2>
                        <p class="text-xs text-slate-500 mt-1">Virtual Account: <span class="font-mono font-bold text-sky-700 dark:text-sky-400">{{ $bill->va_number }}</span></p>
                    </div>

                    <div class="text-left sm:text-right">
                        @if($bill->status === 'paid')
                            <span class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-black bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300">
                                LUNAS (PAID)
                            </span>
                        @else
                            <span class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-black bg-rose-100 text-rose-800 dark:bg-rose-900/40 dark:text-rose-300">
                                BELUM LUNAS (UNPAID)
                            </span>
                        @endif
                        <p class="text-xs text-slate-400 mt-2">Periode: {{ $bill->paymentPeriod->name }}</p>
                    </div>
                </div>

                <!-- Invoice Meta Details -->
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 py-5 border-b border-slate-100 dark:border-gray-700 text-xs">
                    <div>
                        <span class="text-slate-400">Nama Mahasiswa:</span>
                        <p class="font-bold text-slate-800 dark:text-white mt-0.5">{{ $bill->mahasiswa->user->name }}</p>
                    </div>
                    <div>
                        <span class="text-slate-400">NIM:</span>
                        <p class="font-mono font-bold text-slate-800 dark:text-white mt-0.5">{{ $bill->mahasiswa->nim }}</p>
                    </div>
                    <div>
                        <span class="text-slate-400">Program Studi:</span>
                        <p class="font-semibold text-slate-800 dark:text-white mt-0.5">{{ $bill->mahasiswa->prodi->nama }}</p>
                    </div>
                    <div>
                        <span class="text-slate-400">Tanggal Terbit:</span>
                        <p class="font-medium text-slate-700 dark:text-gray-300 mt-0.5">{{ $bill->created_at->format('d M Y') }}</p>
                    </div>
                    <div>
                        <span class="text-slate-400">Batas Jatuh Tempo:</span>
                        <p class="font-medium text-slate-700 dark:text-gray-300 mt-0.5">{{ $bill->due_date ? \Carbon\Carbon::parse($bill->due_date)->format('d M Y') : '-' }}</p>
                    </div>
                    <div>
                        <span class="text-slate-400">Keterangan:</span>
                        <p class="font-medium text-slate-700 dark:text-gray-300 mt-0.5">{{ $bill->description }}</p>
                    </div>
                </div>

                <!-- Cost Summary -->
                <div class="pt-5 space-y-3">
                    <div class="flex justify-between text-sm text-slate-600 dark:text-gray-300">
                        <span>Biaya Penyelenggaraan Pendidikan (UKT)</span>
                        <span class="font-bold text-slate-900 dark:text-white text-base">Rp {{ number_format($bill->amount, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between pt-3 border-t border-slate-200 dark:border-gray-700 text-sm font-semibold">
                        <span class="text-slate-700 dark:text-gray-300">Status Tagihan</span>
                        <span class="{{ $bill->status === 'paid' ? 'text-emerald-600' : 'text-rose-600 dark:text-rose-400' }}">
                            {{ $bill->status === 'paid' ? 'LUNAS (Terbayar Penuh)' : 'BELUM DIBAYAR' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Riwayat Pembayaran Mahasiswa -->
            <div class="card-saas p-6 dark:bg-gray-800">
                <h3 class="text-base font-bold text-slate-900 dark:text-white mb-4">Riwayat Transaksi Terverifikasi</h3>
                @if(count($paymentsWithVerification) > 0)
                <div class="space-y-3">
                    @foreach($paymentsWithVerification as $item)
                        @php $p = $item['payment']; $valid = $item['is_valid']; @endphp
                        <div class="p-4 rounded-xl border border-slate-200 dark:border-gray-700 bg-slate-50 dark:bg-gray-900/40">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                                <div>
                                    <span class="font-mono text-xs font-bold text-slate-900 dark:text-white block">{{ $p->reference_number }}</span>
                                    <span class="text-xs text-slate-400">{{ $p->payment_date->format('d M Y, H:i:s') }} &bull; {{ $p->payment_method }}</span>
                                </div>
                                <div class="text-left sm:text-right">
                                    <span class="text-base font-bold text-emerald-600 dark:text-emerald-400">Rp {{ number_format($p->amount_paid, 0, ',', '.') }}</span>
                                    @if($valid)
                                        <span class="block text-[10px] font-bold text-emerald-600 uppercase tracking-wide">&check; Keamanan Terverifikasi</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                @else
                <p class="text-sm text-slate-400 text-center py-6">Belum ada riwayat pembayaran pada invoice ini.</p>
                @endif
            </div>
        </div>

        <!-- Sidebar Area: Form Bayar Mandiri / Simulasi -->
        <div>
            @if($bill->status !== 'paid')
            <div class="card-saas p-6 dark:bg-gray-800 border-2 border-sky-500/30 sticky top-24">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-xl bg-sky-100 dark:bg-sky-900/50 text-sky-600 flex items-center justify-center font-bold">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-900 dark:text-white text-base">Konfirmasi Pembayaran</h4>
                        <p class="text-xs text-slate-500">Simulasi / Bayar mandiri secara online</p>
                    </div>
                </div>

                <form action="{{ route('mahasiswa.tagihan.pay', $bill->id) }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-gray-300 uppercase mb-1">Nominal Pembayaran (Rp) <span class="text-rose-500">*</span></label>
                        <input type="number" name="amount_paid" required readonly value="{{ $bill->amount }}" class="input-saas w-full px-3 py-2 text-sm dark:bg-gray-900 dark:border-gray-700 font-bold bg-slate-100 dark:bg-gray-800 cursor-not-allowed">
                        <p class="text-[11px] text-slate-400 mt-1">Sesuai ketetapan Rektorat, pembayaran UKT wajib lunas penuh (tidak ada sistem cicilan).</p>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-gray-300 uppercase mb-1">Pilihan Saluran Pembayaran <span class="text-rose-500">*</span></label>
                        <select name="payment_method" required class="input-saas w-full px-3 py-2 text-sm dark:bg-gray-900 dark:border-gray-700">
                            <option value="Virtual Account - Mandiri">Virtual Account - Mandiri</option>
                            <option value="Virtual Account - BNI">Virtual Account - BNI</option>
                            <option value="Virtual Account - BRI">Virtual Account - BRI</option>
                            <option value="QRIS Online">QRIS Online / E-Wallet</option>
                            <option value="Transfer Bank Mandiri">Transfer Bank Mandiri</option>
                        </select>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="text-xs font-semibold text-slate-700 dark:text-gray-300 uppercase">Payment Key Unik <span class="text-rose-500">*</span></label>
                            <span class="text-[10px] text-emerald-600 font-semibold">Idempotency Lock</span>
                        </div>
                        <input type="text" name="payment_key" readonly value="{{ $suggestedPaymentKey }}" class="input-saas w-full px-3 py-2 font-mono text-xs bg-slate-100 dark:bg-gray-900 dark:border-gray-700 cursor-not-allowed">
                        <p class="text-[11px] text-slate-400 mt-1">Sistem membaca key transaksi ini untuk menjamin tidak terjadi pembayaran ganda (2 kali).</p>
                    </div>

                    <div class="p-3 bg-sky-50 dark:bg-sky-900/20 border border-sky-200 dark:border-sky-800 rounded-xl text-sky-800 dark:text-sky-300 text-xs">
                        <p class="font-semibold mb-0.5">Keamanan Berlapis:</p>
                        <p>Kunci unik ini langsung dikunci oleh server. Jika key ini di-submit ulang, server secara otomatis akan menolaknya.</p>
                    </div>

                    <button type="submit" class="w-full btn-primary-saas py-3 rounded-xl text-sm font-bold shadow-md">
                        Konfirmasi & Bayar Sekarang
                    </button>
                </form>
            </div>
            @else
            <div class="card-saas p-6 dark:bg-gray-800 border-2 border-emerald-500/30 text-center">
                <div class="w-14 h-14 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto mb-3">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </div>
                <h4 class="font-bold text-slate-900 dark:text-white text-lg">Tagihan Telah Lunas</h4>
                <p class="text-xs text-slate-500 mt-1">Seluruh kewajiban pembayaran untuk periode ini telah diselesaikan. Anda dapat mengisi KRS dan mengikuti perkuliahan dengan lancar.</p>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
