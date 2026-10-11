<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Bill extends Model
{
    use HasFactory;

    protected $table = 'bills';

    protected $fillable = [
        'invoice_number',
        'va_number',
        'payment_period_id',
        'mahasiswa_id',
        'amount',
        'status',
        'due_date',
        'description',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'due_date' => 'date',
    ];

    public function paymentPeriod()
    {
        return $this->belongsTo(PaymentPeriod::class);
    }

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function getTotalPaidAttribute(): float
    {
        return (float) $this->payments()->where('status', 'success')->sum('amount_paid');
    }

    public function getRemainingAmountAttribute(): float
    {
        $totalPaid = $this->total_paid;
        return max(0, (float) $this->amount - $totalPaid);
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public static function generateInvoiceNumber(int $periodId, int $mahasiswaId): string
    {
        do {
            $inv = sprintf('INV-%s-%03d-%04d', date('Ym'), $periodId, $mahasiswaId % 10000);
            if (self::where('invoice_number', $inv)->exists()) {
                $inv = sprintf('INV-%s-%04d-%s', date('Ym'), $mahasiswaId, strtoupper(Str::random(4)));
            }
        } while (self::where('invoice_number', $inv)->exists());

        return $inv;
    }

    public static function generateVaNumber(string $nim): string
    {
        // Format Virtual Account: 8899 + sanitized NIM
        $cleanNim = preg_replace('/[^0-9]/', '', $nim);
        if (strlen($cleanNim) < 6) {
            $cleanNim = str_pad($cleanNim, 8, '0', STR_PAD_LEFT);
        }
        return '8899' . substr($cleanNim, -10);
    }
}
