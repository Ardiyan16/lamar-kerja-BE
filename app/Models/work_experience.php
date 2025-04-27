<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class work_experience extends Model
{
    protected $fillable = [
        'user_id',
        'company_name',
        'position',
        'date_start',
        'date_end',
        'still_working',
        'information'
    ];
}
