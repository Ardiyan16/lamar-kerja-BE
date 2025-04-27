<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class job_post extends Model
{
    protected $fillable = [
        'company_id',
        'title',
        'range_salary_1',
        'range_salary_2',
        'field_work_id',
        'sub_field_work_id',
        'type_work',
        'work_policy',
        'work_experience',
        'minimum_study',
        'age',
        'allowances_and_benefits',
        'skill',
        'description',
        'status',
        'post_date',
        'post_expired_date'
    ];
}
