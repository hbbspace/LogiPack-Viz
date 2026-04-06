<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'city',
        'address',
        'phone',
        'is_active',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relationships
    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function packagesFromOrigin()
    {
        return $this->hasMany(Package::class, 'branch_origin_id');
    }

    public function packagesFromDestination()
    {
        return $this->hasMany(Package::class, 'branch_destination_id');
    }

    public function packings()
    {
        return $this->hasMany(Packing::class);
    }

    public function deliveriesFromOrigin()
    {
        return $this->hasMany(Delivery::class, 'branch_origin_id');
    }

    public function deliveriesToDestination()
    {
        return $this->hasMany(Delivery::class, 'branch_destination_id');
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

    public function scopeByCode($query, $code)
    {
        return $query->where('code', $code);
    }
}