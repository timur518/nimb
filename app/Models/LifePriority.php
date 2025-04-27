<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LifePriority extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'sphere', 'why_important', 'goal_id'];

    public function goal()
    {
        return $this->belongsTo(Goals::class);
    }
}
