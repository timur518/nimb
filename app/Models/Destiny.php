<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static \Illuminate\Database\Eloquent\Builder|static where(string $column, mixed $operator = null, mixed $value = null)
 * @method static static create(array $attributes = [])
 */

class Destiny extends Model
{
    protected $fillable = [
        'user_id',
        'mission',
        'vocation',
        'passion',
        'profession',
        'destiny',
        'gpt_analyse'

    ];

    protected $guarded = ['id'];

    protected $hidden = ['created_at', 'updated_at'];
}
