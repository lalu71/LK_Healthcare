<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BloodRequest extends Model
{
    protected $fillable = ['user_id', 'patient_name', 'blood_group', 'units','hospital', 'contact_phone', 'needed_by', 'reason', 'status',
    ];

    protected $casts = [
        'needed_by' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
