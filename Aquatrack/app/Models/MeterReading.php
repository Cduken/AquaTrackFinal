<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MeterReading extends Model
{
    use HasFactory;

    protected static function boot()
    {
        parent::boot();

        static::updating(function ($reading) {
            if ($reading->isDirty('amount')) {
            }
        });
    }

    protected $fillable = [
        'user_id',
        'staff_id',
        'billing_month',
        'reading_date',
        'previous_reading',
        'reading',
        'consumption',
        'amount',
        'status',
        'due_date', // ← ADD THIS LINE - THIS IS THE FIX!
        'period',
        'period_type',
        'amount_updated',
        'viewed_by_staff',
        'staff_viewed_at'
    ];

    protected $casts = [
        'reading_date' => 'datetime:Y-m-d',
        'due_date' => 'datetime:Y-m-d', // ← ADD THIS TOO
        'reading' => 'float',
        'consumption' => 'float',
        'amount' => 'float',
        'status' => 'string',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    public function getStatusAttribute()
    {
        if (isset($this->attributes['status'])) {
            return $this->attributes['status'];
        }

        // Example: Mark as Overdue if reading_date is past and amount is unpaid
        if ($this->reading_date->isPast() && $this->amount > 0) {
            return 'Overdue';
        }

        return 'Pending';
    }
}
