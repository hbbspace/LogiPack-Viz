<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackingGaHistory extends Model
{
    use HasFactory;

    protected $table = 'packing_ga_history';

    protected $fillable = [
        'packing_id',
        'generation',
        'chromosome',
        'fitness_score',
        'volume_utilization'
    ];

    protected $casts = [
        'chromosome' => 'array',
        'fitness_score' => 'decimal:2',
        'volume_utilization' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relationships
    public function packing()
    {
        return $this->belongsTo(Packing::class);
    }
}