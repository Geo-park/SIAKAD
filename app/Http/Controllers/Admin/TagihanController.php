<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\Fakultas;
use App\Models\Mahasiswa;
use App\Models\Payment;
use App\Models\PaymentPeriod;
use App\Models\Prodi;
use App\Services\PaymentService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TagihanController extends Controller
{
    protected PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function index(Request $request)
    {
        $query = Bill::with(['mahasiswa.user', 'mahasiswa.prodi.fakultas', 'paymentPeriod', 'payments']);

        // Scope by fakultas if user is akademik with fakultas_id
        if (auth()->user()->role === 'akademik' && auth()->user()->fakultas_id) {
            $query->whereHas('mahasiswa.prodi', function ($q) {
                $q->where('fakultas_id', auth()->user()->fakultas_id);
            });
        } elseif ($request->filled('fakultas_id')) {
            $query->whereHas('mahasiswa.prodi', function ($q) use ($request) {
                $q->where('fakultas_id', $request->fakultas_id);
            });
        }

        // Filter by prodi
        if ($request->filled('prodi_id')) {
            $query->whereHas('mahasiswa', function ($q) use ($request) {
                $q->where('prodi_id', $request->prodi_id);
            });
        }

        // Filter by payment period
        if ($request->filled('payment_period_id')) {
            $query->where('payment_period_id', $request->payment_period_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search by NIM or Nama
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhere('va_number', 'like', "%{$search}%")
                  ->orWhereHas('mahasiswa', function ($mq) use ($search) {
                      $mq->where('nim', 'like', "%{$search}%")
                         ->orWhereHas('user', fn($uq) => $uq->where('name', 'like', "%{$search}%"));
                  });
            });
        }

        // Summary Statistics (calculated on the filtered query clone)
        $statsQuery = clone $query;
        $totalTagihan = (float) $statsQuery->sum('amount');
        
        $totalTerbayar = (float) DB::table('payments')
            ->join('bills', 'payments.bill_id', '=', 'bills.id')
            ->where('payments.status', 'success')
            ->when($request->filled('payment_period_id'), fn($q) => $q->where('bills.payment_period_id', $request->payment_period_id))
            ->when($request->filled('status'), fn($q) => $q->where('bills.status', $request->status))
            ->sum('payments.amount_paid');

        $totalBillsCount = (clone $query)->count();
        $paidBillsCount = (clone $query)->where('status', 'paid')->count();
        $unpaidBillsCount = (clone $query)->where('status', 'unpaid')->count();

        $bills = $query->latest()->paginate(15)->withQueryString();

        $fakultasList = Fakultas::with('prodi')->get();
        $paymentPeriodsList = PaymentPeriod::orderBy('is_active', 'desc')->latest()->get();

        return view('admin.tagihan.index', compact(
            'bills',
            'fakultasList',
            'paymentPeriodsList',
            'totalTagihan',
            'totalTerbayar',
            'totalBillsCount',
            'paidBillsCount',
            'unpaidBillsCount'
        ));
    }

    public function show(Bill $bill)
    {
        $bill->load(['mahasiswa.user', 'mahasiswa.prodi.fakultas', 'paymentPeriod', 'payments.user']);

        // Check if user is akademik and bill is within faculty
        if (auth()->user()->role === 'akademik' && auth()->user()->fakultas_id) {
            if ($bill->mahasiswa->prodi->fakultas_id !== auth()->user()->fakultas_id) {
                abort(403, 'Anda tidak memiliki hak akses ke data tagihan fakultas lain.');
            }
        }

        // Verify security hash for each payment
        $paymentsWithVerification = $bill->payments->map(function ($payment) {
            return [
                'payment' => $payment,
                'is_valid' => $payment->verifyIntegrity(),
            ];
        });

        return view('admin.tagihan.show', compact('bill', 'paymentsWithVerification'));
    }

    public function storeMass(Request $request)
    {
        $request->validate([
            'payment_period_id' => 'required|exists:payment_periods,id',
            'amount' => 'required|numeric|min:10000',
            'due_date' => 'nullable|date',
            'fakultas_id' => 'nullable|exists:fakultas,id',
            'prodi_id' => 'nullable|exists:prodi,id',
            'description' => 'nullable|string|max:255',
        ]);

        // Restrict akademik to their own faculty
        $fakultasId = $request->fakultas_id;
        if (auth()->user()->role === 'akademik' && auth()->user()->fakultas_id) {
            $fakultasId = auth()->user()->fakultas_id;
        }

        try {
            $result = $this->paymentService->generateMassBills(
                (int) $request->payment_period_id,
                (float) $request->amount,
                $request->due_date,
                $fakultasId ? (int) $fakultasId : null,
                $request->prodi_id ? (int) $request->prodi_id : null,
                $request->description
            );

            return redirect()->route('admin.tagihan.index')
                ->with('success', sprintf(
                    'Berhasil membuat %d tagihan baru. (%d mahasiswa dilewati karena sudah memiliki tagihan pada periode ini).',
                    $result['created'],
                    $result['skipped']
                ));
        } catch (Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal membuat tagihan massal: ' . $e->getMessage());
        }
    }

    public function processPayment(Request $request, Bill $bill)
    {
        $request->validate([
            'amount_paid' => 'required|numeric|min:1',
            'payment_method' => 'required|string|max:64',
            'reference_number' => 'required|string|max:128',
            'notes' => 'nullable|string|max:255',
        ]);

        try {
            $payment = $this->paymentService->processPayment(
                $bill,
                (float) $request->amount_paid,
                $request->payment_method,
                $request->reference_number,
                auth()->id(),
                $request->notes
            );

            return redirect()->back()->with('success', sprintf(
                'Pembayaran sebesar Rp %s berhasil dicatat dengan Payment Key [%s]. Keamanan transaksi diverifikasi (Hash: %s).',
                number_format($payment->amount_paid, 0, ',', '.'),
                $payment->reference_number,
                substr($payment->security_hash, 0, 12) . '...'
            ));
        } catch (Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function destroy(Bill $bill)
    {
        if ($bill->payments()->where('status', 'success')->exists()) {
            return redirect()->back()->with('error', 'Tagihan yang sudah memiliki riwayat pembayaran tidak dapat dihapus.');
        }

        $bill->delete();
        return redirect()->route('admin.tagihan.index')->with('success', 'Tagihan berhasil dihapus.');
    }
}
