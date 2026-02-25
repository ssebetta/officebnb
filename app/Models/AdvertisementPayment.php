<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdvertisementPayment extends Model
{
    protected $fillable = [
        'advertisement_id',
        'payment_gateway',
        'transaction_id',
        'amount_cents',
        'status',
        'payment_details',
    ];

    protected $casts = [
        'payment_details' => 'json',
    ];

    public function advertisement()
    {
        return $this->belongsTo(Advertisement::class);
    }
}
