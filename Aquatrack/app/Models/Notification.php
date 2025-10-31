<?php
// app/Models/Notification.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'message',
        'type',
        'action_url',
        'read_at',
        'important',
        'data' // for storing additional data if needed
    ];

    protected $casts = [
        'read_at' => 'datetime',
        'important' => 'boolean',
        'data' => 'array'
    ];

    // Relationship with User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Accessor for unread status
    public function getUnreadAttribute()
    {
        return is_null($this->read_at);
    }

    // Scope for unread notifications
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    // Scope for read notifications
    public function scopeRead($query)
    {
        return $query->whereNotNull('read_at');
    }

    // Scope for important notifications
    public function scopeImportant($query)
    {
        return $query->where('important', true);
    }

    // Mark as read
    public function markAsRead()
    {
        $this->update(['read_at' => now()]);
    }

    // Mark as unread
    public function markAsUnread()
    {
        $this->update(['read_at' => null]);
    }
}
