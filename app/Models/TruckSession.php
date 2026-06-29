<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DateTimeInterface;

class TruckSession extends Model
{
    protected $table = 'truck_sessions';

    protected $fillable = [
        'truck_id',
        'driver_id',
        'destination',
        'purpose',
        'checkout_time',
        'checkin_time',
        'checkout_by',
        'checkin_by',
    ];

    protected $casts = [
        'checkout_time' => 'datetime',
        'checkin_time' => 'datetime',
    ];

    public function truck()
    {
        return $this->belongsTo(Truck::class, 'truck_id');
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class, 'driver_id');
    }

    public function checkoutUser()
    {
        return $this->belongsTo(User::class, 'checkout_by');
    }

    public function checkinUser()
    {
        return $this->belongsTo(User::class, 'checkin_by');
    }

    /**
     * Prepare a date for array / JSON serialization.
     */
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
