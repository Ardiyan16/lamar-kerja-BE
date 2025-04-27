<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class apply_jobs extends Model
{
    protected $fillable = [
        'user_id',
        'post_id',
        'date_apply',
        'status',
    ];
}
