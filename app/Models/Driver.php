<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    protected $table = 'drivers';

    protected $fillable = [
        'name',
        'phone',
    ];

    public function sessions()
    {
        return $this->hasMany(TruckSession::class, 'driver_id');
    }
}
