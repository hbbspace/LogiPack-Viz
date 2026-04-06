<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackingPackage extends Model
{
    use HasFactory;

    protected $table = 'packing_packages';

    protected $fillable = [
        'packing_id',
        'package_id',
        'is_placed',
        'position_x',
        'position_y',
        'position_z',
        'orientation'
    ];

    protected $casts = [
        'is_placed' => 'boolean',
        'position_x' => 'integer',
        'position_y' => 'integer',
        'position_z' => 'integer',
        'orientation' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relationships
    public function packing()
    {
        return $this->belongsTo(Packing::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    // Helper methods
    public function getPositionAttribute()
    {
        if (!$this->is_placed) {
            return null;
        }
        
        return [
            'x' => $this->position_x,
            'y' => $this->position_y,
            'z' => $this->position_z
        ];
    }

    public function getDimensionsAttribute()
    {
        return [
            'length' => $this->package->length,
            'width' => $this->package->width,
            'height' => $this->package->height
        ];
    }
}