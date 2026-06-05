<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prescription extends Model
{
    protected $fillable = [
        'appointment_id', 'patient_id', 'doctor_id',
        'prescription_code', 'diagnosis', 'advice', 'follow_up_date', 'status', 'payment_status',
    ];

    protected $casts = [
        'follow_up_date' => 'date',
    ];

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function items()
    {
        return $this->hasMany(PrescriptionItem::class);
    }

    public function payments()
    {
        return $this->morphMany(Payment::class, 'payable');
    }

    public function getEstimatedTotalAttribute(): float
    {
        return (float) $this->items->sum(function ($i) {
            // 1) If linked to catalog, use that price.
            if ($i->medicine && $i->medicine->price > 0) {
                return $i->medicine->price;
            }
            // 2) Try matching the typed name against the catalog (case-insensitive).
            $byName = Medicine::whereRaw('LOWER(name) LIKE ?', [strtolower(trim($i->medicine_name)).'%'])
                ->value('price');
            if ($byName !== null && $byName > 0) {
                return (float) $byName;
            }
            // 3) Fallback so the pharmacist sees a starting figure they can edit.
            return 50.0;
        });
    }
}
