<?php

namespace App\Models;

use App\Enums\GoalsBanStatus;
use Illuminate\Database\Eloquent\Model;

class Goals extends Model
{
    protected $table = 'goals';

    // add fillable
    protected $fillable = [
        'user_id',
        'name',
        'description',
        'picture_url',
        'goal_status',
        'deadline_date',
        'amount',
        'amount_coll'
    ];

    // Статусы для стортировочного канбана
    protected $casts = [
        'sortban_status' => GoalsBanStatus::class,
        'deadline_date' => 'date',
    ];

    // отсылка к пользователю
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // add guaded
    protected $guarded = ['id'];
    // add hidden
    protected $hidden = ['created_at', 'updated_at'];
}
