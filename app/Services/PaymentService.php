<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Bill;
use App\Models\Mahasiswa;
use App\Models\Payment;
use App\Models\PaymentPeriod;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    /**
     * Process payment with strict idempotency key check, pessimistic locking, and cryptographic security hash
     *
     * @throws Exception
     */
    public function processPayment(
        Bill $bill,
        float $amount,
        string $paymentMethod,
        string $paymentKey,
        ?int $userId = null,
        ?string $notes = null
    ): Payment {
        $paymentKey = trim($paymentKey);

        if (empty($paymentKey)) {
            throw new Exception('Payment Key unik wajib disertakan.');
        }

        // 1. Pre-validation idempotency check: reject if key has already been processed
        if (Payment::where('idempotency_key', $paymentKey)->orWhere('reference_number', $paymentKey)->exists()) {
            throw new Exception("Idempotency Key [{$paymentKey}] sudah pernah diproses sebelumnya! Sistem menolak pembayaran untuk mencegah transaksi ganda.");
        }

        // 2. Pre-validation bill status: reject if bill is already fully paid
        if ($bill->status === 'paid') {
            throw new Exception('Tagihan ini sudah lunas. Tidak dapat menerima pembayaran tambahan.');
        }

        // 3. Atomically execute payment using pessimistic database lock
        return DB::transaction(function () use ($bill, $amount, $paymentMethod, $paymentKey, $userId, $notes) {
            // Lock the bill record to serialize concurrent payment attempts
            /** @var Bill $lockedBill */
            $lockedBill = Bill::where('id', $bill->id)->lockForUpdate()->firstOrFail();

            if ($lockedBill->status === 'paid') {
                throw new Exception('Tagihan ini sudah lunas. Transaksi dibatalkan.');
            }

            // Lock and double check idempotency key inside transaction
            if (Payment::where('idempotency_key', $paymentKey)->orWhere('reference_number', $paymentKey)->lockForUpdate()->exists()) {
                throw new Exception("Idempotency key [{$paymentKey}] sedang atau telah diproses. Pembayaran ganda dicegah.");
            }

            $billAmount = (float) $lockedBill->amount;

            // Enforce strictly no installments: must pay exact bill amount set by rektorat
            if (abs($amount - $billAmount) > 0.01) {
                throw new Exception(
                    sprintf(
                        'Pembayaran harus lunas penuh sesuai ketetapan Rektorat sebesar Rp %s. Sistem tidak menerima pembayaran cicilan.',
                        number_format($billAmount, 0, ',', '.')
                    )
                );
            }

            // Generate cryptographic HMAC security hash
            $securityHash = Payment::generateSecurityHash($lockedBill->id, $amount, $paymentKey);

            // Record payment transaction with explicit Idempotency Key
            $payment = Payment::create([
                'bill_id' => $lockedBill->id,
                'amount_paid' => $amount,
                'payment_date' => now(),
                'payment_method' => $paymentMethod,
                'reference_number' => $paymentKey,
                'idempotency_key' => $paymentKey,
                'security_hash' => $securityHash,
                'status' => 'success',
                'user_id' => $userId,
                'notes' => $notes,
            ]);

            // Update bill status to paid (lunas penuh)
            $lockedBill->update(['status' => 'paid']);

            // Audit logging
            try {
                ActivityLog::create([
                    'user_id' => $userId,
                    'action' => 'process_payment',
                    'model_type' => Payment::class,
                    'model_id' => $payment->id,
                    'changes' => [
                        'bill_id' => $lockedBill->id,
                        'invoice' => $lockedBill->invoice_number,
                        'amount_paid' => $amount,
                        'payment_key' => $paymentKey,
                        'idempotency_key' => $paymentKey,
                        'new_bill_status' => $newStatus,
                        'hash' => $securityHash,
                    ],
                    'ip_address' => request()->ip() ?? '127.0.0.1',
                    'user_agent' => request()->userAgent() ?? 'System',
                ]);
            } catch (\Throwable $e) {
                Log::warning('ActivityLog error during payment: ' . $e->getMessage());
            }

            return $payment;
        });
    }

    /**
     * Generate bills in bulk for active students in a payment period
     *
     * @return array{created: int, skipped: int}
     */
    public function generateMassBills(
        int $paymentPeriodId,
        float $amount,
        ?string $dueDate = null,
        ?int $fakultasId = null,
        ?int $prodiId = null,
        ?string $description = null
    ): array {
        $period = PaymentPeriod::findOrFail($paymentPeriodId);

        $query = Mahasiswa::query()->where('status', 'aktif');

        if ($fakultasId) {
            $query->whereHas('prodi', fn($q) => $q->where('fakultas_id', $fakultasId));
        }

        if ($prodiId) {
            $query->where('prodi_id', $prodiId);
        }

        $allStudents = $query->get();
        $createdCount = 0;
        $skippedCount = 0;

        DB::transaction(function () use ($allStudents, $period, $amount, $dueDate, $description, &$createdCount, &$skippedCount) {
            foreach ($allStudents as $mhs) {
                // Check if student already has a bill for this period
                $exists = Bill::where('payment_period_id', $period->id)
                    ->where('mahasiswa_id', $mhs->id)
                    ->exists();

                if ($exists) {
                    $skippedCount++;
                    continue;
                }

                $invoiceNumber = Bill::generateInvoiceNumber($period->id, $mhs->id);
                $vaNumber = Bill::generateVaNumber($mhs->nim);

                Bill::create([
                    'invoice_number' => $invoiceNumber,
                    'va_number' => $vaNumber,
                    'payment_period_id' => $period->id,
                    'mahasiswa_id' => $mhs->id,
                    'amount' => $amount,
                    'status' => 'unpaid',
                    'due_date' => $dueDate ?: $period->end_date,
                    'description' => $description ?: ('Tagihan Biaya Kuliah - ' . $period->name),
                ]);

                $createdCount++;
            }
        });

        return [
            'created' => $createdCount,
            'skipped' => $skippedCount,
        ];
    }
}
