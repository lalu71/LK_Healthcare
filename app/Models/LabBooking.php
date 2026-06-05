<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LabBooking extends Model
{
    protected $fillable = [
        'patient_id', 'lab_test_id', 'booking_code', 'booking_date',
        'status', 'payment_status', 'amount', 'result_file', 'notes',
    ];

    protected $casts = [
        'booking_date' => 'datetime',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function labTest()
    {
        return $this->belongsTo(LabTest::class);
    }
}
