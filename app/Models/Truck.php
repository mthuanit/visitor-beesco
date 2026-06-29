<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Truck extends Model
{
    protected $table = 'trucks';

    public const STATUS_INSIDE = 'inside';
    public const STATUS_OUTSIDE = 'outside';

    protected $fillable = [
        'name',
        'license_plate',
        'status',
    ];

    public function sessions()
    {
        return $this->hasMany(TruckSession::class, 'truck_id');
    }

    public function activeSession()
    {
        return $this->hasOne(TruckSession::class, 'truck_id')->whereNull('checkin_time');
    }
}
