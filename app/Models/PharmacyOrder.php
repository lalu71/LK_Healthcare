<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PharmacyOrder extends Model
{
    protected $fillable = [
        'patient_id', 'order_code', 'status', 'payment_status',
        'subtotal', 'delivery_fee', 'total',
        'delivery_address', 'delivery_phone', 'notes',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function items()
    {
        return $this->hasMany(PharmacyOrderItem::class);
    }
}
