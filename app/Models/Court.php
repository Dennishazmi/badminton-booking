<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Court extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'type', 'description', 'price_per_hour', 'is_available'
    ];

    protected $casts = ['is_available' => 'boolean', 'price_per_hour' => 'decimal:2'];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function isBookedAt(string $date, string $timeSlot): bool
    {
        return $this->bookings()
            ->where('booking_date', $date)
            ->where('time_slot', $timeSlot)
            ->whereIn('status', ['confirmed', 'pending'])
            ->exists();
    }
}
