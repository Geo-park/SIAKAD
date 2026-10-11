<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentPeriod;
use Illuminate\Http\Request;

class PaymentPeriodController extends Controller
{
    public function index()
    {
        $periods = PaymentPeriod::withCount('bills')
            ->withSum('bills as total_bills_amount', 'amount')
            ->latest()
            ->paginate(10);

        return view('admin.tagihan.periods', compact('periods'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:128',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'is_active' => 'nullable|boolean',
        ]);

        $isActive = $request->boolean('is_active');

        // If setting as active, optionally deactivate other periods
        if ($isActive) {
            PaymentPeriod::where('is_active', true)->update(['is_active' => false]);
        }

        PaymentPeriod::create([
            'name' => $request->name,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'is_active' => $isActive,
        ]);

        return redirect()->back()->with('success', 'Periode pembayaran berhasil ditambahkan.');
    }

    public function update(Request $request, PaymentPeriod $paymentPeriod)
    {
        $request->validate([
            'name' => 'required|string|max:128',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'is_active' => 'nullable|boolean',
        ]);

        $isActive = $request->boolean('is_active');

        if ($isActive && !$paymentPeriod->is_active) {
            PaymentPeriod::where('is_active', true)->update(['is_active' => false]);
        }

        $paymentPeriod->update([
            'name' => $request->name,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'is_active' => $isActive,
        ]);

        return redirect()->back()->with('success', 'Periode pembayaran berhasil diperbarui.');
    }

    public function toggleActive(PaymentPeriod $paymentPeriod)
    {
        $newStatus = !$paymentPeriod->is_active;

        if ($newStatus) {
            PaymentPeriod::where('is_active', true)->update(['is_active' => false]);
        }

        $paymentPeriod->update(['is_active' => $newStatus]);

        return redirect()->back()->with('success', 'Status keaktifan periode pembayaran berhasil diubah.');
    }
}
