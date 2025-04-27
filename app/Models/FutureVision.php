<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FutureVision extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'vision_text'
    ];

    protected $guarded = ['id'];
    protected $hidden = ['created_at', 'updated_at'];
}
