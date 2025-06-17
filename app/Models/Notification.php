<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory; // Assuming you might want to use factories later

    protected $fillable = [
        'user_id', // The user who will receive the notification
        'type',    // Could be the class name of a Laravel Notification, or a custom string
        'data',    // JSON column to store notification-specific data
        'read_at', // Timestamp when the notification was read by the user
    ];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
    ];

    /**
     * Get the user that the notification belongs to.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
