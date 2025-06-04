<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class village extends Model
{
    protected $fillable = [
        'id',
        'district_id',
        'name'
    ];
}
