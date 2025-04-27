<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class company_profile extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'address',
        'type_industry',
        'total_employee',
        'about_us',
        'corporate_culture',
        'link',
        'social_media',
        'gallery',
        'total_job_posts',
        'is_premium'
    ];
}
