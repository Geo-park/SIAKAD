<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\Payment;
use App\Models\PaymentPeriod;
use App\Services\PaymentService;
use Exception;
use Illuminate\Http\Request;

class TagihanController extends Controller
{
    protected PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function index()
    {
        $mahasiswa = auth()->user()->mahasiswa;

        if (!$mahasiswa) {
            abort(403, 'Profil mahasiswa tidak ditemukan.');
        }

        $activePeriod = PaymentPeriod::where('is_active', true)->first();

        $bills = Bill::with(['paymentPeriod', 'payments'])
            ->where('mahasiswa_id', $mahasiswa->id)
            ->latest()
            ->get();

        $activeBill = $bills->first(function ($bill) use ($activePeriod) {
            return $activePeriod && $bill->payment_period_id === $activePeriod->id;
        }) ?? $bills->first();

        // Pre-generate unique payment key suggestion for safe transaction
        $suggestedPaymentKey = Payment::generateUniqueKey();

        return view('mahasiswa.tagihan.index', compact(
            'mahasiswa',
            'bills',
            'activeBill',
            'activePeriod',
            'suggestedPaymentKey'
        ));
    }

    public function show(Bill $bill)
    {
        $mahasiswa = auth()->user()->mahasiswa;

        if (!$mahasiswa || $bill->mahasiswa_id !== $mahasiswa->id) {
            abort(403, 'Anda tidak memiliki hak akses ke data tagihan ini.');
        }

        $bill->load(['paymentPeriod', 'payments']);

        $paymentsWithVerification = $bill->payments->map(function ($payment) {
            return [
                'payment' => $payment,
                'is_valid' => $payment->verifyIntegrity(),
            ];
        });

        $suggestedPaymentKey = Payment::generateUniqueKey();

        return view('mahasiswa.tagihan.show', compact('bill', 'paymentsWithVerification', 'suggestedPaymentKey'));
    }

    public function pay(Request $request, Bill $bill)
    {
        $mahasiswa = auth()->user()->mahasiswa;

        if (!$mahasiswa || $bill->mahasiswa_id !== $mahasiswa->id) {
            abort(403, 'Akses ditolak.');
        }

        $request->validate([
            'amount_paid' => 'required|numeric|min:1000',
            'payment_method' => 'required|string|max:64',
            'payment_key' => 'required|string|max:128',
        ]);

        try {
            $payment = $this->paymentService->processPayment(
                $bill,
                (float) $request->amount_paid,
                $request->payment_method,
                $request->payment_key,
                auth()->id(),
                'Pembayaran Mandiri oleh Mahasiswa'
            );

            return redirect()->route('mahasiswa.tagihan.show', $bill->id)->with('success', sprintf(
                'Pembayaran sebesar Rp %s berhasil diverifikasi dengan Payment Key [%s]. Transaksi aman & sah.',
                number_format($payment->amount_paid, 0, ',', '.'),
                $payment->reference_number
            ));
        } catch (Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }
}
