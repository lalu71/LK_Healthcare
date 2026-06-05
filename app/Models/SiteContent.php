<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteContent extends Model
{
    protected $table = 'site_content';
    
    protected $fillable = ['site_name','site_title','site_description','help_contact','follow_by','site_email','site_address'];

}
