<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class company_reoport extends Model
{
    protected $fillable = [
        'company_id',
        'user_id',
        'reason',
    ];
}
