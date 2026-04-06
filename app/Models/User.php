<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'username',
        'password',
        'name',
        'email',
        'phone',
        'level',
        'is_active',
        'last_activity_at',
        'last_login_at',
        'branch_id',
        'created_by',
        'updated_by'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_activity_at' => 'datetime',
        'last_login_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relationships
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function packings()
    {
        return $this->hasMany(Packing::class, 'user_id');
    }

    public function createdPackings()
    {
        return $this->hasMany(Packing::class, 'created_by');
    }

    public function updatedPackings()
    {
        return $this->hasMany(Packing::class, 'updated_by');
    }

    public function createdDeliveries()
    {
        return $this->hasMany(Delivery::class, 'created_by');
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

    public function scopeAdmin($query)
    {
        return $query->where('level', 'admin');
    }

    public function scopeUser($query)
    {
        return $query->where('level', 'user');
    }

    public function scopeByBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    // Helper methods
    public function isAdmin()
    {
        return $this->level === 'admin';
    }

    public function isUser()
    {
        return $this->level === 'user';
    }
}