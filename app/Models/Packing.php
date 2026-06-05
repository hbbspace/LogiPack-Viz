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
        'chromosome',
        'visualization_file_path',
        'execution_time_ms',
        'notes',
        'container_id',
        'user_id',
        'ga_parameter_id',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'volume_utilization' => 'decimal:2',
        'weight_utilization' => 'decimal:2',
        'fitness_score' => 'decimal:2',
        'chromosome' => 'array',
        'execution_time_ms' => 'integer',
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

    public function gaParameter()
    {
        return $this->belongsTo(GaParameter::class);
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
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeHighFitness($query, $threshold = 90)
    {
        return $query->where('fitness_score', '>=', $threshold);
    }
}