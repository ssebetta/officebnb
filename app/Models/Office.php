<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Office extends Model
{
    /** @use HasFactory<\Database\Factories\OfficeFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'owner_id',
        'city',
        'country',
        'address',
        'description',
        'amenities',
        'image_urls',
        'workspace_type',
        'size_sqft',
        'meeting_rooms',
        'desks',
        'timezone',
        'capacity',
        'daily_rate',
        'is_active',
    ];

    protected $casts = [
        'amenities' => 'array',
        'image_urls' => 'array',
        'is_active' => 'boolean',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function managers()
    {
        return $this->belongsToMany(User::class, 'office_user')->withTimestamps();
    }

    public function invitations()
    {
        return $this->hasMany(OfficeInvitation::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function advertisements()
    {
        return $this->hasMany(Advertisement::class);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
