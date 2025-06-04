<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class company_profile extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'address',
        'provinces',
        'regency',
        'district',
        'telp_number',
        'type_industry',
        'total_employee',
        'about_us',
        'corporate_culture',
        'link',
        'social_media',
        'logo_profile',
        'gallery',
        'motto',
        'total_job_posts',
        'status_profile',
        'is_premium'
    ];
}
