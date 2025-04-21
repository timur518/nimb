<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\SortBanStatus;

class MainKanban extends Model
{
    protected $table = 'main_kanbans';
    protected $fillable = [
        'user_id',
        'name',
        'description',
        'sortban_status',
        'kanban_status',
        'tasks_ids',
        'category_id',
        'planned_date',
        'deadline_date',
        'amount',
        'amount_coll',
        'progress',
    ];

    // Статусы для стортировочного канбана
    protected $casts = [
        'sortban_status' => SortBanStatus::class,
        'planned_date' => 'date',
        'deadline_date' => 'date',
    ];

    public function tasks(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Task::class);
    }

    // отсылка к пользователю
    // Пример связей (по желанию)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(KanbanCategories::class);
    }

    // add guaded
    protected $guarded = ['id'];

    // add hidden
    protected $hidden = ['created_at', 'updated_at'];
}
