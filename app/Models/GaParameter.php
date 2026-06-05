<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GaParameter extends Model
{
    use HasFactory;

    protected $table = 'ga_parameters';

    protected $fillable = [
        'name',
        'population_size',
        'generation_limit',
        'crossover_rate',
        'mutation_rate',
        'is_active',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'population_size' => 'integer',
        'generation_limit' => 'integer',
        'crossover_rate' => 'float',
        'mutation_rate' => 'float',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Boot method untuk memastikan hanya 1 parameter yang aktif
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            if ($model->is_active) {
                static::where('is_active', true)
                    ->where('id', '!=', $model->id)
                    ->update(['is_active' => false]);
            }
        });
    }

    /**
     * Relationships
     */
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

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeDefault($query)
    {
        return $query->where('name', 'Default GA');
    }

    /**
     * Helper Methods
     */
    public function getFormattedParamsAttribute(): array
    {
        return [
            'Population Size' => number_format($this->population_size),
            'Generation Limit' => number_format($this->generation_limit),
            'Crossover Rate' => ($this->crossover_rate * 100) . '%',
            'Mutation Rate' => ($this->mutation_rate * 100) . '%',
        ];
    }

    public function activate(): void
    {
        $this->update(['is_active' => true]);
    }

    public function deactivate(): void
    {
        $this->update(['is_active' => false]);
    }

    public function isDefault(): bool
    {
        return $this->name === 'Default GA';
    }

    /**
     * Get the active GA parameter
     */
    public static function getActive(): ?self
    {
        return static::where('is_active', true)->first();
    }

    /**
     * Get default GA parameter (fallback jika tidak ada yang active)
     */
    public static function getDefault(): ?self
    {
        return static::where('name', 'Default GA')->first();
    }
}