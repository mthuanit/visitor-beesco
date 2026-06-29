<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    use HasFactory;

    public const STATUS_AVAILABLE = 'available';
    public const STATUS_IN_USE = 'in_use';
    public const STATUS_LOST = 'lost';

    protected $fillable = [
        'code',
        'status',
    ];

    public function activeSession()
    {
        return $this->hasOne(VisitorSession::class, 'barcode', 'code')->whereNull('checkout_time');
    }
}
