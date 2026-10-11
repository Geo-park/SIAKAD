<?php

namespace Database\Seeders;

use App\Models\Bill;
use App\Models\Mahasiswa;
use App\Models\PaymentPeriod;
use App\Services\PaymentService;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create or ensure Payment Periods
        $pPast = PaymentPeriod::firstOrCreate(
            ['name' => 'Semester Genap 2023/2024'],
            [
                'start_date' => '2024-02-01',
                'end_date' => '2024-06-30',
                'is_active' => false,
            ]
        );

        $pActive = PaymentPeriod::firstOrCreate(
            ['name' => 'Semester Ganjil 2024/2025'],
            [
                'start_date' => '2024-09-01',
                'end_date' => '2025-01-31',
                'is_active' => true,
            ]
        );

        // 2. Generate bills for existing active students
        $paymentService = app(PaymentService::class);
        $result = $paymentService->generateMassBills(
            $pActive->id,
            3500000,
            '2024-10-31',
            null,
            null,
            'Biaya Kuliah Tunggal (UKT) Semester Ganjil 2024/2025'
        );

        $this->command->info(sprintf(
            'PaymentSeeder executed: %d bills created, %d skipped for period %s.',
            $result['created'],
            $result['skipped'],
            $pActive->name
        ));
    }
}
