<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Advertisement extends Model
{
    protected $fillable = [
        'office_id',
        'type',
        'image_url',
        'start_date',
        'end_date',
        'status',
        'amount_cents',
        'payment_method',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function office()
    {
        return $this->belongsTo(Office::class);
    }

    public function payment()
    {
        return $this->hasOne(AdvertisementPayment::class);
    }

    public function isActive()
    {
        return $this->status === 'active' && now()->isBetween($this->start_date, $this->end_date);
    }
}
