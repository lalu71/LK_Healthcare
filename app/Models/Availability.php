<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Availability extends Model
{
    protected $fillable = ['doctor_id', 'day_of_week', 'start_time', 'end_time', 'slot_minutes', 'is_active'];

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public static function dayName(int $dow): string
    {
        return ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'][$dow] ?? '';
    }
}
