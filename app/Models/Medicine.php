<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Medicine extends Model
{
    protected $fillable = [
        'name', 'category', 'manufacturer', 'description', 'price',
        'stock', 'unit', 'requires_prescription', 'is_active',
    ];
}
