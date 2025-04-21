<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KanbanCategories extends Model
{
    // add fillable
    protected $fillable = [
        'user_id',
        'name',
    ];
    // add guaded

    public function projects()
    {
        return $this->hasMany(MainKanban::class);
    }

    protected $guarded = ['id'];
    // add hidden
    protected $hidden = ['created_at', 'updated_at'];
}
