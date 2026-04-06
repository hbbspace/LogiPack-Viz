<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Packing extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'volume_utilization',
        'weight_utilization',
        'fitness_score',
        'center_of_gravity_x',
        'center_of_gravity_y',
        'center_of_gravity_z',
        'visualization_file_path',
        'algorithm_params',
        'raw_result',
        'notes',
        'container_id',
        'branch_id',
        'user_id',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'volume_utilization' => 'decimal:2',
        'weight_utilization' => 'decimal:2',
        'fitness_score' => 'decimal:2',
        'center_of_gravity_x' => 'decimal:2',
        'center_of_gravity_y' => 'decimal:2',
        'center_of_gravity_z' => 'decimal:2',
        'algorithm_params' => 'array',
        'raw_result' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relationships
    public function container()
    {
        return $this->belongsTo(Container::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function packingPackages()
    {
        return $this->hasMany(PackingPackage::class);
    }

    public function packages()
    {
        return $this->belongsToMany(Package::class, 'packing_packages')
                    ->withPivot('is_placed', 'position_x', 'position_y', 'position_z', 'orientation')
                    ->withTimestamps();
    }

    public function placedPackages()
    {
        return $this->belongsToMany(Package::class, 'packing_packages')
                    ->wherePivot('is_placed', true)
                    ->withPivot('position_x', 'position_y', 'position_z', 'orientation');
    }

    public function unplacedPackages()
    {
        return $this->belongsToMany(Package::class, 'packing_packages')
                    ->wherePivot('is_placed', false);
    }

    public function delivery()
    {
        return $this->hasOne(Delivery::class);
    }

    public function gaHistories()
    {
        return $this->hasMany(PackingGaHistory::class);
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
    public function scopeByBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeHighFitness($query, $threshold = 90)
    {
        return $query->where('fitness_score', '>=', $threshold);
    }

    // Helper methods
    public function getCenterOfGravityAttribute()
    {
        return [
            'x' => $this->center_of_gravity_x,
            'y' => $this->center_of_gravity_y,
            'z' => $this->center_of_gravity_z
        ];
    }

    public function isDelivered()
    {
        return $this->delivery()->exists() && $this->delivery->status === 'completed';
    }
}