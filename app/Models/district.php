<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class district extends Model
{
    protected $fillable = [
        'id',
        'regency_id',
        'name'
    ];
}
