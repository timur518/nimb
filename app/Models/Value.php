<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Value extends Model
{
    //

    // add fillable
    protected $fillable = [
        'user_id',
        'sphere',
        'value'
    ];

    protected $guarded = ['id'];
    protected $hidden = ['created_at', 'updated_at'];
}
