<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DateTimeInterface;

class VisitorSession extends Model
{
    protected $table = 'visitor_sessions';
    protected $fillable = [
        'barcode', 'name', 'cccd', 'phone', 'company', 'meet_person', 'vehicle', 'photo', 'photo_checkout', 'portrait_photo', 'portrait_photo_checkout', 'checkin_time', 'checkout_time'
    ];

    protected $casts = [
        'checkin_time' => 'datetime',
        'checkout_time' => 'datetime',
    ];

    /**
     * Prepare a date for array / JSON serialization.
     */
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
