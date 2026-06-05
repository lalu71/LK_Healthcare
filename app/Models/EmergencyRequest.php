<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmergencyRequest extends Model
{
    protected $fillable = [
        'user_id', 'contact_name', 'contact_phone', 'location',
        'latitude', 'longitude', 'description', 'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
