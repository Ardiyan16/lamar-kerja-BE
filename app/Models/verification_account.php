<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class verification_account extends Model
{
    protected $fillable = [
        'email',
        'token',
        'time',
    ];
}
