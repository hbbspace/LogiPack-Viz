<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Container extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type_code',
        'length',
        'width',
        'height',
        'volume_max',
        'weight_max',
        'image_url',
        'description',
        'is_active',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'length' => 'integer',
        'width' => 'integer',
        'height' => 'integer',
        'volume_max' => 'integer',
        'weight_max' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Boot method untuk auto-calculate volume
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            $model->volume_max = $model->length * $model->width * $model->height;
        });
    }

    // Relationships
    public function packings()
    {
        return $this->hasMany(Packing::class);
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
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, $typeCode)
    {
        return $query->where('type_code', $typeCode);
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

    public function getVolumeAttribute()
    {
        return $this->volume_max;
    }
}