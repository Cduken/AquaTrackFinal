<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MeterReading extends Model
{
    use HasFactory;

    // In your MeterReading.php model, add this:
    protected static function boot()
    {
        parent::boot();

        static::updating(function ($reading) {
            // Check if amount is being changed by admin
            if ($reading->isDirty('amount')) {
                // Reset some flag to trigger notification (we'll handle this differently)
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
    ];

    protected $casts = [
        'reading_date' => 'datetime:Y-m-d',
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
