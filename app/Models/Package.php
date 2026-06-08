<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use HasFactory;

    protected $fillable = [
        'tracking_number',
        'length',
        'width',
        'height',
        'volume',
        'weight',
        'status',
        'notes',
        'batch_import_id',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'length' => 'decimal:2',
        'width' => 'decimal:2',
        'height' => 'decimal:2',
        'volume' => 'decimal:2',
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
    public function batchImport()
    {
        return $this->belongsTo(BatchImport::class);
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
        if ($this->status === 'pending') {
            $this->update(['status' => 'packed']);
            return true;
        }
        return false;
    }
}