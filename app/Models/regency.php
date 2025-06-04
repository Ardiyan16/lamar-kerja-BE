<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class regency extends Model
{
    protected $fillable = [
        'id',
        'province_id',
        'name'
    ];
}
