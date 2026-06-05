<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BloodDonor extends Model
{
    protected $fillable = [
        'user_id', 'name', 'blood_group', 'phone', 'city',
        'last_donated_at', 'is_available',
    ];

    protected $casts = [
        'last_donated_at' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
