<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'patient_id', 'dob', 'gender', 'blood_group',
        'allergies', 'medical_history', 'emergency_contact', 'aadhaar_number',
    ];

    protected $casts = [
        'dob' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function prescriptions()
    {
        return $this->hasMany(Prescription::class);
    }

    public function medicalRecords()
    {
        return $this->hasMany(MedicalRecord::class);
    }

    public function labBookings()
    {
        return $this->hasMany(LabBooking::class);
    }

    public function pharmacyOrders()
    {
        return $this->hasMany(PharmacyOrder::class);
    }

    public function getAgeAttribute(): ?int
    {
        return $this->dob?->age;
    }
}
