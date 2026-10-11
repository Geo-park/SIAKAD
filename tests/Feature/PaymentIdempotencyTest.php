<?php

use App\Models\Bill;
use App\Models\Mahasiswa;
use App\Models\Payment;
use App\Models\PaymentPeriod;
use App\Models\Prodi;
use App\Models\Fakultas;
use App\Models\User;
use App\Services\PaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->paymentService = app(PaymentService::class);

    $this->user = User::factory()->create(['role' => 'mahasiswa']);
    $this->fakultas = Fakultas::create(['nama' => 'Fakultas Teknik']);
    $this->prodi = Prodi::create(['nama' => 'Teknik Informatika', 'fakultas_id' => $this->fakultas->id]);
    $this->mahasiswa = Mahasiswa::create([
        'user_id' => $this->user->id,
        'nim' => '202401099',
        'prodi_id' => $this->prodi->id,
        'angkatan' => 2024,
        'status' => 'aktif',
    ]);

    $this->period = PaymentPeriod::create([
        'name' => 'Semester Ganjil 2024/2025',
        'start_date' => '2024-09-01',
        'end_date' => '2025-01-31',
        'is_active' => true,
    ]);

    $this->bill = Bill::create([
        'invoice_number' => 'INV-TEST-001',
        'va_number' => '8899202401099',
        'payment_period_id' => $this->period->id,
        'mahasiswa_id' => $this->mahasiswa->id,
        'amount' => 3000000,
        'status' => 'unpaid',
        'description' => 'Tagihan UKT Test',
    ]);
});

it('processes payment successfully with unique idempotency key', function () {
    $idempotencyKey = Payment::generateIdempotencyKey();

    $payment = $this->paymentService->processPayment(
        $this->bill,
        3000000,
        'Virtual Account',
        $idempotencyKey,
        $this->user->id,
        'Pembayaran Lunas Penuh'
    );

    expect($payment)->not->toBeNull();
    expect($payment->idempotency_key)->toBe($idempotencyKey);
    expect($payment->reference_number)->toBe($idempotencyKey);
    expect($payment->verifyIntegrity())->toBeTrue();
    expect($this->bill->fresh()->status)->toBe('paid');
    expect($this->bill->fresh()->remaining_amount)->toBe(0.0);
});

it('strictly rejects partial payment or installment not equal to rectorate amount', function () {
    $idempotencyKey = Payment::generateIdempotencyKey();

    // Attempt partial payment of 1,000,000 when bill is 3,000,000
    expect(function () use ($idempotencyKey) {
        $this->paymentService->processPayment(
            $this->bill,
            1000000,
            'Virtual Account',
            $idempotencyKey
        );
    })->toThrow(Exception::class, 'Pembayaran harus lunas penuh sesuai ketetapan Rektorat sebesar Rp 3.000.000. Sistem tidak menerima pembayaran cicilan.');

    // Status remains unpaid
    expect($this->bill->fresh()->status)->toBe('unpaid');
    expect($this->bill->fresh()->total_paid)->toBe(0.0);
});

it('strictly rejects duplicate payment attempt with the same idempotency key', function () {
    $idempotencyKey = Payment::generateIdempotencyKey();

    // First full payment succeeds
    $this->paymentService->processPayment(
        $this->bill,
        3000000,
        'Virtual Account',
        $idempotencyKey
    );

    // Second payment with the same key MUST throw exception
    expect(function () use ($idempotencyKey) {
        $this->paymentService->processPayment(
            $this->bill->fresh(),
            3000000,
            'Virtual Account',
            $idempotencyKey
        );
    })->toThrow(Exception::class, "Idempotency Key [{$idempotencyKey}] sudah pernah diproses sebelumnya! Sistem menolak pembayaran untuk mencegah transaksi ganda.");
});

it('strictly rejects payment when bill is already fully paid', function () {
    // Pay full amount first
    $key1 = Payment::generateIdempotencyKey();
    $this->paymentService->processPayment(
        $this->bill,
        3000000,
        'Virtual Account',
        $key1
    );

    expect($this->bill->fresh()->status)->toBe('paid');

    // Attempt extra payment with new key
    $key2 = Payment::generateIdempotencyKey();
    expect(function () use ($key2) {
        $this->paymentService->processPayment(
            $this->bill->fresh(),
            3000000,
            'Virtual Account',
            $key2
        );
    })->toThrow(Exception::class, 'Tagihan ini sudah lunas. Tidak dapat menerima pembayaran tambahan.');
});

it('strictly rejects duplicate idempotency key submitted via HTTP endpoint', function () {
    $this->actingAs($this->user);

    $idempotencyKey = Payment::generateIdempotencyKey();

    // 1st request via HTTP POST with full payment succeeds
    $response1 = $this->post(route('mahasiswa.tagihan.pay', $this->bill->id), [
        'amount_paid' => 3000000,
        'payment_method' => 'Virtual Account',
        'payment_key' => $idempotencyKey,
    ]);
    $response1->assertRedirect();
    $response1->assertSessionHas('success');

    // 2nd request via HTTP POST with identical key is rejected
    $response2 = $this->post(route('mahasiswa.tagihan.pay', $this->bill->id), [
        'amount_paid' => 3000000,
        'payment_method' => 'Virtual Account',
        'payment_key' => $idempotencyKey,
    ]);
    $response2->assertSessionHas('error');
    expect(session('error'))->toContain("Idempotency Key [{$idempotencyKey}] sudah pernah diproses sebelumnya");
});

