<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class report_post extends Model
{
    protected $fillable = [
        'post_id',
        'user_id',
        'reason'
    ];
}
