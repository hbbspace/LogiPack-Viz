<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use HasFactory;

    protected $fillable = [
        'tracking_number',
        'shipper',
        'shipper_address',
        'recipient',
        'recipient_address',
        'length',
        'width',
        'height',
        'volume',
        'weight',
        'status',
        'delivered_at',
        'notes',
        'branch_origin_id',
        'branch_destination_id',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'length' => 'integer',
        'width' => 'integer',
        'height' => 'integer',
        'volume' => 'integer',
        'weight' => 'decimal:2',
        'delivered_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Boot method untuk auto-calculate volume
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            $model->volume = $model->length * $model->width * $model->height;
        });

        // Auto-generate tracking number jika belum ada
        static::creating(function ($model) {
            if (empty($model->tracking_number)) {
                $model->tracking_number = 'PKG-' . strtoupper(uniqid());
            }
        });
    }

    // Relationships
    public function branchOrigin()
    {
        return $this->belongsTo(Branch::class, 'branch_origin_id');
    }

    public function branchDestination()
    {
        return $this->belongsTo(Branch::class, 'branch_destination_id');
    }

    public function packingPackages()
    {
        return $this->hasMany(PackingPackage::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopePacked($query)
    {
        return $query->where('status', 'packed');
    }

    public function scopeDelivered($query)
    {
        return $query->where('status', 'delivered');
    }

    public function scopeByBranch($query, $branchId)
    {
        return $query->where('branch_origin_id', $branchId);
    }

    public function scopeAvailableForPacking($query, $branchId)
    {
        return $query->where('branch_origin_id', $branchId)
                     ->where('status', 'pending');
    }

    // Helper methods
    public function getDimensionsAttribute()
    {
        return [
            'length' => $this->length,
            'width' => $this->width,
            'height' => $this->height
        ];
    }

    public function getVolumeCubicAttribute()
    {
        return $this->volume;
    }

    public function markAsPacked()
    {
        $this->update(['status' => 'packed']);
    }

    public function markAsDelivered()
    {
        $this->update([
            'status' => 'delivered',
            'delivered_at' => now()
        ]);
    }
}