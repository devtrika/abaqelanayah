<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class CourseEnrollment extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'user_id',
        'course_id',
        'enrolled_at',
        'status',
        'progress_percentage',
        'completed_at',
        'payment_method',
        'payment_status',
        'amount_paid',
        'payment_reference',
        'bank_account_id',
        'payment_completed_at',
        'total_time_spent',
        'last_accessed_at',
        'completed_stages_count',
        'payment_method_id',
        // Bank transfer data fields
        'sender_bank_name',
        'sender_account_holder_name',
        'sender_account_number',
        'sender_iban',
        'transfer_amount',
        'transfer_reference',
        'transfer_date',
        'bank_transfer_status',
        'verified_at',
        'verified_by',
        'rejected_at',
        'rejected_by',
        'admin_notes'
    ];

    protected $casts = [
        'enrolled_at' => 'datetime',
        'completed_at' => 'datetime',
        'payment_completed_at' => 'datetime',
        'last_accessed_at' => 'datetime',
        'progress_percentage' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'total_time_spent' => 'integer',
        'completed_stages_count' => 'integer',
        // Bank transfer data casts
        'transfer_amount' => 'decimal:2',
        'transfer_date' => 'date',
        'verified_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    /**
     * Get the user that owns the enrollment
     */
    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    /**
     * Get the course for this enrollment
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the bank account for bank transfer payments
     */
    public function bankAccount()
    {
        return $this->belongsTo(ProviderBankAccount::class, 'bank_account_id');
    }

    /**
     * Get the admin user who verified the bank transfer
     */
    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Get the admin user who rejected the bank transfer
     */
    public function rejectedBy()
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    /**
     * Get the completed stages for this enrollment
     */
    public function completedStages()
    {
        return $this->belongsToMany(CourseStage::class, 'course_stage_completions', 'enrollment_id', 'stage_id')
                    ->withPivot('completed_at', 'time_spent', 'notes')
                    ->withTimestamps();
    }

    /**
     * Get the stage completions for this enrollment
     */
    public function stageCompletions()
    {
        return $this->hasMany(CourseStageCompletion::class, 'enrollment_id');
    }

    /**
     * Scope for search functionality
     */
    public function scopeSearch($query, $searchArray = [])
    {
        if ($searchArray && !empty(array_filter($searchArray))) {
            $query->where(function ($query) use ($searchArray) {
                foreach ($searchArray as $key => $value) {
                    if ($key == 'id' && !empty($value)) {
                        $query->where('id', 'like', '%' . $value . '%');
                    } elseif ($key == 'course_id' && !empty($value)) {
                        $query->where('course_id', $value);
                    } elseif ($key == 'user_id' && !empty($value)) {
                        $query->where('user_id', $value);
                    } elseif ($key == 'payment_method' && !empty($value)) {
                        $query->where('payment_method', $value);
                    } elseif ($key == 'payment_status' && !empty($value)) {
                        $query->where('payment_status', $value);
                    } elseif ($key == 'status' && !empty($value)) {
                        $query->where('status', $value);
                    } elseif ($key == 'from_date' && !empty($value)) {
                        $query->whereDate('enrolled_at', '>=', $value);
                    } elseif ($key == 'to_date' && !empty($value)) {
                        $query->whereDate('enrolled_at', '<=', $value);
                    } elseif ($key == 'amount_from' && !empty($value)) {
                        $query->where('amount_paid', '>=', $value);
                    } elseif ($key == 'amount_to' && !empty($value)) {
                        $query->where('amount_paid', '<=', $value);
                    } elseif ($key == 'created_at_min' && !empty($value)) {
                        $query->whereDate('enrolled_at', '>=', $value);
                    } elseif ($key == 'created_at_max' && !empty($value)) {
                        $query->whereDate('enrolled_at', '<=', $value);
                    } elseif ($key == 'order') {
                        // Skip order parameter
                    }
                }
            });
        }

        return $query->orderBy('enrolled_at', request()->searchArray && request()->searchArray['order'] ? request()->searchArray['order'] : 'DESC');
    }

    /**
     * Check if enrollment is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if course is completed
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed' || $this->progress_percentage >= 100;
    }

    /**
     * Mark enrollment as completed
     */
    public function markAsCompleted()
    {
        $this->update([
            'status' => 'completed',
            'progress_percentage' => 100,
            'completed_at' => now(),
        ]);

        return $this;
    }

    /**
     * Update progress percentage
     */
    public function updateProgress(float $percentage)
    {
        $percentage = max(0, min(100, $percentage)); // Ensure between 0-100

        $this->update([
            'progress_percentage' => $percentage,
            'status' => $percentage >= 100 ? 'completed' : 'active',
            'completed_at' => $percentage >= 100 ? now() : null,
        ]);

        return $this;
    }

    /**
     * Scope to get active enrollments
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to get completed enrollments
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope to get enrollments with pending bank transfers
     */
    public function scopePendingBankTransfer($query)
    {
        return $query->where('payment_method_id', 5)
                    ->where('bank_transfer_status', 'pending');
    }

    /**
     * Scope to get enrollments with verified bank transfers
     */
    public function scopeVerifiedBankTransfer($query)
    {
        return $query->where('payment_method_id', 5)
                    ->where('bank_transfer_status', 'verified');
    }

    /**
     * Scope to get enrollments with rejected bank transfers
     */
    public function scopeRejectedBankTransfer($query)
    {
        return $query->where('payment_method_id', 5)
                    ->where('bank_transfer_status', 'rejected');
    }

    /**
     * Check if payment is completed
     */
    public function isPaymentCompleted(): bool
    {
        return $this->payment_status === 'paid';
    }

    /**
     * Check if bank transfer is verified
     */
    public function isBankTransferVerified(): bool
    {
        return $this->bank_transfer_status === 'verified';
    }

    /**
     * Check if bank transfer is rejected
     */
    public function isBankTransferRejected(): bool
    {
        return $this->bank_transfer_status === 'rejected';
    }

    /**
     * Check if bank transfer is pending
     */
    public function isBankTransferPending(): bool
    {
        return $this->bank_transfer_status === 'pending';
    }

    /**
     * Verify bank transfer
     */
    public function verifyBankTransfer(int $adminUserId, ?string $adminNotes = null): self
    {
        $updateData = [
            'bank_transfer_status' => 'verified',
            'verified_at' => now(),
            'verified_by' => $adminUserId,
            'payment_status' => 'paid',
            'status' => 'active',
            'payment_completed_at' => now(),
        ];

        // Only add admin_notes if provided
        if ($adminNotes !== null) {
            $updateData['admin_notes'] = $adminNotes;
        }

        $this->update($updateData);

        return $this;
    }

    /**
     * Reject bank transfer
     */
    public function rejectBankTransfer(int $adminUserId, ?string $adminNotes = null): self
    {
        $updateData = [
            'bank_transfer_status' => 'rejected',
            'rejected_at' => now(),
            'rejected_by' => $adminUserId,
            'payment_status' => 'failed',
            'status' => 'cancelled',
        ];

        // Only add admin_notes if provided
        if ($adminNotes !== null) {
            $updateData['admin_notes'] = $adminNotes;
        }

        $this->update($updateData);

        return $this;
    }

    /**
     * Check if payment is pending
     */
    public function isPaymentPending(): bool
    {
        return $this->payment_status === 'pending';
    }

    /**
     * Mark payment as completed
     */
    public function markPaymentAsCompleted(?string $paymentReference = null)
    {
        $this->update([
            'payment_status' => 'paid',
            'status' => 'active',
            'payment_reference' => $paymentReference ?? $this->payment_reference,
            'payment_completed_at' => now(),
        ]);

        return $this;
    }

    /**
     * Mark payment as failed
     */
    public function markPaymentAsFailed()
    {
        $this->update([
            'payment_status' => 'failed',
            'status' => 'cancelled',
        ]);

        return $this;
    }

    /**
     * Scope to get paid enrollments
     */
    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }

    /**
     * Scope to get pending payment enrollments
     */
    public function scopePendingPayment($query)
    {
        return $query->where('payment_status', 'pending');
    }

    /**
     * Calculate and update progress percentage based on completed stages
     */
    public function calculateProgress()
    {
        $totalStages = $this->course->stages()->count();

        if ($totalStages == 0) {
            $this->update([
                'progress_percentage' => 0,
                'completed_stages_count' => 0
            ]);
            return 0;
        }

        $completedStagesCount = $this->stageCompletions()
                                    ->whereNotNull('completed_at')
                                    ->count();
        $progress = round(($completedStagesCount / $totalStages) * 100, 2);

        $this->update([
            'progress_percentage' => $progress,
            'completed_stages_count' => $completedStagesCount,
            'last_accessed_at' => now()
        ]);

        // Auto-complete if 100%
        if ($progress >= 100 && $this->status !== 'completed') {
            $this->update([
                'status' => 'completed',
                'completed_at' => now()
            ]);
        }

        return $progress;
    }

    /**
     * Create stage completion records for all course stages when enrolling
     */
    public function createStageCompletionRecords()
    {
        $stages = $this->course->stages()->get();

        foreach ($stages as $stage) {
            CourseStageCompletion::firstOrCreate([
                'enrollment_id' => $this->id,
                'stage_id' => $stage->id,
            ], [
                'completed_at' => null,
                'time_spent' => 0,
                'last_watch_time' => 0,
                'notes' => null
            ]);
        }

        return $this;
    }

    /**
     * Update watch time for a specific stage
     */
    public function updateStageWatchTime($stageId, $watchTime, $timeSpent = null)
    {
        $completion = $this->stageCompletions()
                          ->where('stage_id', $stageId)
                          ->first();

        if (!$completion) {
            throw new \InvalidArgumentException('Stage completion record not found');
        }

        $updateData = ['last_watch_time' => $watchTime];

        if ($timeSpent !== null) {
            $updateData['time_spent'] = $timeSpent;
        }

        $completion->update($updateData);

        // Update total time spent if provided
        if ($timeSpent !== null) {
            $this->update(['total_time_spent' => $this->stageCompletions()->sum('time_spent')]);
        }

        return $completion;
    }

    /**
     * Mark a stage as completed
     */
    public function completeStage($stageId, $timeSpent = 0, $notes = null)
    {
        $completion = $this->stageCompletions()
                          ->where('stage_id', $stageId)
                          ->first();

        if (!$completion) {
            throw new \InvalidArgumentException('Stage completion record not found');
        }

        // Update completion record
        $completion->update([
            'completed_at' => now(),
            'time_spent' => $timeSpent,
            'notes' => $notes
        ]);

        // Update total time spent
        $this->update(['total_time_spent' => $this->stageCompletions()->sum('time_spent')]);

        // Recalculate progress
        return $this->calculateProgress();
    }

    /**
     * Check if a stage is completed
     */
    public function isStageCompleted($stageId)
    {
        return $this->stageCompletions()
                   ->where('stage_id', $stageId)
                   ->whereNotNull('completed_at')
                   ->exists();
    }

    /**
     * Get the next uncompleted stage
     */
    public function getNextStage()
    {
        $completedStageIds = $this->stageCompletions()
                                 ->whereNotNull('completed_at')
                                 ->pluck('stage_id')
                                 ->toArray();

        return $this->course->stages()
                    ->whereNotIn('id', $completedStageIds)
                    ->orderBy('order')
                    ->first();
    }

    /**
     * Get completion details for a specific stage
     */
    public function getStageCompletion($stageId)
    {
        return $this->stageCompletions()->where('stage_id', $stageId)->first();
    }

    /**
     * Get all stages with completion status
     */
    public function getStagesWithProgress()
    {
        $stages = $this->course->stages()->orderBy('order')->get();
        $completions = $this->stageCompletions()->get()->keyBy('stage_id');

        return $stages->map(function ($stage) use ($completions) {
            $completion = $completions->get($stage->id);
            $stage->is_completed = $completion && $completion->completed_at !== null;
            $stage->last_watch_time = $completion ? $completion->last_watch_time : 0;
            $stage->completion_details = $completion;
            return $stage;
        });
    }

    /**
     * Reset progress (remove all stage completions)
     */
    public function resetProgress()
    {
        $this->stageCompletions()->delete();

        $this->update([
            'progress_percentage' => 0,
            'completed_stages_count' => 0,
            'total_time_spent' => 0,
            'status' => 'active',
            'completed_at' => null
        ]);

        return $this;
    }

    /**
     * Get formatted total time spent
     */
    public function getFormattedTimeSpentAttribute()
    {
        if (!$this->total_time_spent) {
            return '0 minutes';
        }

        $hours = floor($this->total_time_spent / 3600);
        $minutes = floor(($this->total_time_spent % 3600) / 60);
        $seconds = $this->total_time_spent % 60;

        $parts = [];
        if ($hours > 0) {
            $parts[] = $hours . ' hour' . ($hours > 1 ? 's' : '');
        }
        if ($minutes > 0) {
            $parts[] = $minutes . ' minute' . ($minutes > 1 ? 's' : '');
        }
        if ($seconds > 0 && $hours == 0) {
            $parts[] = $seconds . ' second' . ($seconds > 1 ? 's' : '');
        }

        return implode(' ', $parts) ?: '0 minutes';
    }

    /**
     * Register media collections for receipt images
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('receipt_images')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'])
            ->singleFile();
    }

    /**
     * Register media conversions for receipt images
     */
    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(300)
            ->height(300)
            ->sharpen(10)
            ->performOnCollections('receipt_images');

        $this->addMediaConversion('preview')
            ->width(800)
            ->height(600)
            ->sharpen(10)
            ->performOnCollections('receipt_images');
    }

    /**
     * Get receipt image URL
     */
    public function getReceiptImageUrlAttribute(): ?string
    {
        $media = $this->getFirstMedia('receipt_images');
        return $media ? $media->getUrl() : null;
    }

    /**
     * Get receipt image thumbnail URL
     */
    public function getReceiptImageThumbUrlAttribute(): ?string
    {
        $media = $this->getFirstMedia('receipt_images');
        return $media ? $media->getUrl('thumb') : null;
    }

    /**
     * Get receipt image preview URL
     */
    public function getReceiptImagePreviewUrlAttribute(): ?string
    {
        $media = $this->getFirstMedia('receipt_images');
        return $media ? $media->getUrl('preview') : null;
    }
}
