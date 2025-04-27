<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class profile_user extends Model
{
    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'place_birth',
        'date_birth',
        'whatsapp',
        'gender',
        'address',
        'postal_code',
        'picture',
        'about_me',
        'education',
        'last_education',
        'skill',
        'field_work_id',
        'position',
        'job_type',
        'salary_expectation',
        'work_city_preference',
        'is_remote',
        'resume',
        'link_portfolio',
        'social_media',
        'certificated',
        'award',
        'total_applications'
    ];
}
