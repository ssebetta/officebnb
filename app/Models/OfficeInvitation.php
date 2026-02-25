<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfficeInvitation extends Model
{
    protected $fillable = [
        'office_id',
        'email',
        'token',
        'invited_by',
        'accepted_at',
    ];

    protected $casts = [
        'accepted_at' => 'datetime',
    ];

    public function office()
    {
        return $this->belongsTo(Office::class);
    }

    public function inviter()
    {
        return $this->belongsTo(User::class, 'invited_by');
    }
}
