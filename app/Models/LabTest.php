<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LabTest extends Model
{
    protected $fillable = ['name', 'category', 'description', 'price', 'duration_hours', 'is_active'];

    public function bookings()
    {
        return $this->hasMany(LabBooking::class);
    }
}
