<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'court_id', 'booking_date', 'time_slot',
        'status', 'amount', 'payment_method', 'payment_status',
        'qr_token', 'notes', 'qr_scanned_at'
    ];

    protected $casts = [
        'booking_date' => 'date',
        'amount'       => 'decimal:2',
        'qr_scanned_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($booking) {
            if (empty($booking->qr_token)) {
                $booking->qr_token = strtoupper(Str::random(10));
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function court()
    {
        return $this->belongsTo(Court::class);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('booking_date', '>=', today())->orderBy('booking_date')->orderBy('time_slot');
    }

    public function scopePast($query)
    {
        return $query->where('booking_date', '<', today())->orderByDesc('booking_date');
    }
}
