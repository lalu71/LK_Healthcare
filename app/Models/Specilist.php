<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Specilist extends Model
{
    protected $table = 'specilist';

    protected $fillable = ['name', 'icon', 'status'];
}
