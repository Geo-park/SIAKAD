<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Payment extends Model
{
    use HasFactory;

    protected $table = 'payments';

    protected $fillable = [
        'bill_id',
        'amount_paid',
        'payment_date',
        'payment_method',
        'reference_number',
        'idempotency_key',
        'security_hash',
        'status',
        'user_id',
        'notes',
    ];

    protected $casts = [
        'amount_paid' => 'decimal:2',
        'payment_date' => 'datetime',
    ];

    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Generate an unpredictable, collision-resistant unique idempotency key
     */
    public static function generateIdempotencyKey(): string
    {
        do {
            $key = 'IDEMP-' . date('YmdHis') . '-' . strtoupper(Str::random(8));
        } while (self::where('idempotency_key', $key)->orWhere('reference_number', $key)->exists());

        return $key;
    }

    /**
     * Generate an unpredictable, collision-resistant unique payment key / reference
     */
    public static function generateUniqueKey(): string
    {
        return self::generateIdempotencyKey();
    }

    /**
     * Cryptographic HMAC SHA256 security hash to prove payment tamper-proof integrity
     */
    public static function generateSecurityHash(int $billId, float $amount, string $referenceNumber): string
    {
        $payload = sprintf('BILL:%d|AMOUNT:%.2f|REF:%s', $billId, $amount, trim($referenceNumber));
        return hash_hmac('sha256', $payload, config('app.key'));
    }

    /**
     * Verify if current payment record integrity matches its security hash
     */
    public function verifyIntegrity(): bool
    {
        if (empty($this->security_hash) || empty($this->reference_number)) {
            return false;
        }

        $expectedHash = self::generateSecurityHash(
            $this->bill_id,
            (float) $this->amount_paid,
            $this->reference_number
        );

        return hash_equals($expectedHash, $this->security_hash);
    }
}
