<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BatchImport extends Model
{
    use HasFactory;

    protected $table = 'batch_imports';

    protected $fillable = [
        'file_path',
        'original_name',
        'total_packages',
        'status',
        'error_message',
        'user_id'
    ];

    protected $casts = [
        'total_packages' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Status constants
     */
    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';

    /**
     * Relationships
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function packages()
    {
        return $this->hasMany(Package::class);
    }

    /**
     * Scopes
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeProcessing($query)
    {
        return $query->where('status', self::STATUS_PROCESSING);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    public function scopeFailed($query)
    {
        return $query->where('status', self::STATUS_FAILED);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Helper Methods
     */
    public function getSuccessfullyImportedPackagesCount(): int
    {
        return $this->packages()->count();
    }

    public function getFailedPackagesCount(): int
    {
        return $this->total_packages - $this->getSuccessfullyImportedPackagesCount();
    }

    public function getBranchAttribute()
    {
        return $this->user->branch;
    }

    public function getBranchNameAttribute(): string
    {
        return $this->user->branch->name ?? 'N/A';
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'warning',
            self::STATUS_PROCESSING => 'info',
            self::STATUS_COMPLETED => 'success',
            self::STATUS_FAILED => 'danger',
            default => 'secondary',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'Pending',
            self::STATUS_PROCESSING => 'Processing',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_FAILED => 'Failed',
            default => ucfirst($this->status),
        };
    }

    public function markAsProcessing(): void
    {
        $this->update(['status' => self::STATUS_PROCESSING]);
    }

    public function markAsCompleted(): void
    {
        $this->update(['status' => self::STATUS_COMPLETED]);
    }

    public function markAsFailed(string $errorMessage): void
    {
        $this->update([
            'status' => self::STATUS_FAILED,
            'error_message' => $errorMessage
        ]);
    }

    public function canBeProcessed(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_FAILED]);
    }

    public function getFileUrlAttribute(): string
    {
        return asset('storage/' . $this->file_path);
    }
}