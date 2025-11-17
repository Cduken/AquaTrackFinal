<?php
// app/Models/WaterRate.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WaterRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'min_consumption',
        'max_consumption',
        'rate_per_cubic',
        'fixed_charge',
        'order',
        'is_active'
    ];

    protected $casts = [
        'min_consumption' => 'decimal:2',
        'max_consumption' => 'decimal:2',
        'rate_per_cubic' => 'decimal:2',
        'fixed_charge' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // Scope for active rates
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope for ordered rates
    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('min_consumption');
    }
}
