<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ThanksDairy extends Model
{
    protected $fillable = [
        'user_id',
        'date',
        'thanks',
        'acceptance',
        'creation',
        'note'
    ];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected $guarded = ['id'];
    protected $hidden = ['created_at', 'updated_at'];
}
