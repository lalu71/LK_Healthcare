<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BloodInventory extends Model
{
    protected $table = 'blood_inventory';
    protected $fillable = ['blood_group', 'units'];
}
