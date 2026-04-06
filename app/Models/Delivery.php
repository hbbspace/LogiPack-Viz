<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    use HasFactory;

    protected $fillable = [
        'delivery_number',
        'status',
        'scheduled_date',
        'completed_date',
        'driver_name',
        'vehicle_plate',
        'notes',
        'packing_id',
        'branch_origin_id',
        'branch_destination_id',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'completed_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Boot method untuk auto-generate delivery number
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->delivery_number)) {
                $model->delivery_number = 'DLV-' . strtoupper(uniqid());
            }
        });

        static::updating(function ($model) {
            // Jika status berubah menjadi completed, update status packages
            if ($model->isDirty('status') && $model->status === 'completed') {
                $model->completed_date = $model->completed_date ?? now();
                
                // Update status semua paket dalam packing menjadi delivered
                $model->packing->packages->each(function ($package) {
                    $package->markAsDelivered();
                });
            }
        });
    }

    // Relationships
    public function packing()
    {
        return $this->belongsTo(Packing::class);
    }

    public function branchOrigin()
    {
        return $this->belongsTo(Branch::class, 'branch_origin_id');
    }

    public function branchDestination()
    {
        return $this->belongsTo(Branch::class, 'branch_destination_id');
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
    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    public function scopeInTransit($query)
    {
        return $query->where('status', 'in_transit');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeByOrigin($query, $branchId)
    {
        return $query->where('branch_origin_id', $branchId);
    }

    public function scopeByDestination($query, $branchId)
    {
        return $query->where('branch_destination_id', $branchId);
    }

    // Helper methods
    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    public function isInTransit()
    {
        return $this->status === 'in_transit';
    }

    public function isScheduled()
    {
        return $this->status === 'scheduled';
    }

    public function complete()
    {
        $this->update(['status' => 'completed']);
    }
}