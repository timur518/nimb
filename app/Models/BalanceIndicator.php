<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static \Illuminate\Database\Eloquent\Builder|static where(string $column, mixed $operator = null, mixed $value = null)
 * @method static static create(array $attributes = [])
 */
class BalanceIndicator extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',

        // Финансы
        'finance_goals', 'finance_learning', 'finance_environment', 'finance_tracking',
        'finance_saving', 'finance_income', 'finance_economy', 'finance_investment',

        // Карьера
        'career_growth', 'career_engagement', 'career_environment', 'career_balance',
        'career_rewards', 'career_goals', 'career_satisfaction',

        // Саморазвитие
        'self_education', 'self_growth', 'self_skills', 'self_creativity',
        'self_social', 'self_planning', 'self_discipline',

        // Духовность и творчество
        'soul_practices', 'soul_creativity', 'soul_knowledge', 'soul_people',
        'soul_nature', 'soul_reflection',

        // Отдых и хобби
        'rest_passive', 'rest_active', 'rest_hobbies', 'rest_social',
        'rest_learning', 'rest_relaxation',

        // Друзья и окружение
        'friends_support', 'friends_meetings', 'friends_culture', 'friends_interests',
        'friends_trust', 'friends_variety',

        // Семья
        'family_emotion', 'family_time', 'family_rituals', 'family_communication',
        'family_support', 'family_health',

        // Здоровье
        'health_physical', 'health_emotional', 'health_social', 'health_sleep',
        'health_prevention', 'health_balance',

        // Анализ GPT
        'gpt_analyse',
    ];
}
