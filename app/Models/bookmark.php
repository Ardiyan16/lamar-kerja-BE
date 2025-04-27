<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class bookmark extends Model
{
    protected $fillable = [
        'user_id',
        'post_job_id',
    ];
}
