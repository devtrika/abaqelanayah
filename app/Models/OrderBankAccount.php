<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderBankAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'sender_bank_name',
        'sender_account_holder_name',
        'sender_account_number',
        'sender_iban',
        'transfer_amount',
        'transfer_reference',
        'transfer_date',
        'status',
        'verified_at',
        'verified_by',
        'rejected_at',
        'rejected_by',
        'admin_notes',
    ];

    protected $casts = [
        'transfer_amount' => 'decimal:2',
        'transfer_date' => 'datetime',
        'verified_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    /**
     * Get the order that owns the bank account transfer
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the admin user who verified the transfer
     */
    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Check if the transfer is pending verification
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }

    /**
     * Check if the transfer is verified
     */
    public function isVerified()
    {
        return $this->status === 'verified';
    }

    /**
     * Check if the transfer is rejected
     */
    public function isRejected()
    {
        return $this->status === 'rejected';
    }

    /**
     * Mark transfer as verified
     */
    public function markAsVerified($adminId = null, $notes = null)
    {
        $this->update([
            'status' => 'verified',
            'verified_at' => now(),
            'verified_by' => $adminId,
            'admin_notes' => $notes,
        ]);
    }

    /**
     * Mark transfer as rejected
     */
    public function markAsRejected($adminId = null, $notes = null)
    {
        $this->update([
            'status' => 'rejected',
            'verified_at' => now(),
            'verified_by' => $adminId,
            'admin_notes' => $notes,
        ]);
    }
}
