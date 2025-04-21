<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\ChatGptAnalyse;
use App\Filament\Widgets\KanbanStats;
use App\Models\BalanceIndicator;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class BalanceTracker extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-pie';
    protected static string $view = 'filament.pages.balance-tracker';
    protected static ?string $navigationGroup = 'Колесо баланса';
    protected static ?string $title = 'Трекер баланса';
    protected static ?int $navigationSort = 2;

    public $summary = [];
    public $charts = [];

    public function mount()
    {
        $user = Auth::user();

        $indicator = BalanceIndicator::where('user_id', $user->id)->first();

        if(!$indicator) {
            $indicator = new \stdClass();

            // Соберем все возможные поля из категорий
            $fields = collect([
                'finance_goals', 'finance_learning', 'finance_environment', 'finance_tracking',
                'finance_saving', 'finance_income', 'finance_economy', 'finance_investment',

                'career_growth', 'career_engagement', 'career_environment', 'career_balance',
                'career_rewards', 'career_goals', 'career_satisfaction',

                'self_education', 'self_growth', 'self_skills', 'self_creativity',
                'self_social', 'self_planning', 'self_discipline',

                'soul_practices', 'soul_creativity', 'soul_knowledge', 'soul_people',
                'soul_nature', 'soul_reflection',

                'rest_passive', 'rest_active', 'rest_hobbies', 'rest_social',
                'rest_learning', 'rest_relaxation',

                'friends_support', 'friends_meetings', 'friends_culture', 'friends_interests',
                'friends_trust', 'friends_variety',

                'family_emotion', 'family_time', 'family_rituals', 'family_communication',
                'family_support', 'family_health',

                'health_physical', 'health_emotional', 'health_social', 'health_sleep',
                'health_prevention', 'health_balance',
            ]);

            // Заполним все поля нулями
            foreach ($fields as $field) {
                $indicator->$field = 0;
            }
        }

        // Суммируем по категориям
        $this->summary = [
            'Финансы' => $this->averageFields($indicator, [
                'finance_goals', 'finance_learning', 'finance_environment', 'finance_tracking',
                'finance_saving', 'finance_income', 'finance_economy', 'finance_investment'
            ]),
            'Карьера' => $this->averageFields($indicator, [
                'career_growth', 'career_engagement', 'career_environment', 'career_balance',
                'career_rewards', 'career_goals', 'career_satisfaction'
            ]),
            'Саморазвитие' => $this->averageFields($indicator, [
                'self_education', 'self_growth', 'self_skills', 'self_creativity',
                'self_social', 'self_planning', 'self_discipline'
            ]),
            'Духовность и творчество' => $this->averageFields($indicator, [
                'soul_practices', 'soul_creativity', 'soul_knowledge', 'soul_people',
                'soul_nature', 'soul_reflection'
            ]),
            'Отдых и хобби' => $this->averageFields($indicator, [
                'rest_passive', 'rest_active', 'rest_hobbies', 'rest_social',
                'rest_learning', 'rest_relaxation'
            ]),
            'Друзья и окружение' => $this->averageFields($indicator, [
                'friends_support', 'friends_meetings', 'friends_culture', 'friends_interests',
                'friends_trust', 'friends_variety'
            ]),
            'Семья' => $this->averageFields($indicator, [
                'family_emotion', 'family_time', 'family_rituals', 'family_communication',
                'family_support', 'family_health'
            ]),
            'Здоровье' => $this->averageFields($indicator, [
                'health_physical', 'health_emotional', 'health_social', 'health_sleep',
                'health_prevention', 'health_balance'
            ]),
        ];

        // Подготовка графиков по категориям
        $this->charts = [
            'chart_finance' => $this->makeChart('Финансы', $indicator, [
                'finance_goals' => 'Цели',
                'finance_learning' => 'Обучение',
                'finance_environment' => 'Окружение',
                'finance_tracking' => 'Отслеживание',
                'finance_saving' => 'Сбережения',
                'finance_income' => 'Доход',
                'finance_economy' => 'Экономия',
                'finance_investment' => 'Инвестиции',
            ]),
            'chart_career' => $this->makeChart('Карьера', $indicator, [
                'career_growth' => 'Рост',
                'career_engagement' => 'Вовлеченность',
                'career_environment' => 'Окружение',
                'career_balance' => 'Баланс',
                'career_rewards' => 'Награды',
                'career_goals' => 'Цели',
                'career_satisfaction' => 'Удовлетворенность',
            ]),
            'chart_self' => $this->makeChart('Саморазвитие', $indicator, [
                'self_education' => 'Образование',
                'self_growth' => 'Рост',
                'self_skills' => 'Навыки',
                'self_creativity' => 'Творчество',
                'self_social' => 'Общение',
                'self_planning' => 'Планирование',
                'self_discipline' => 'Дисциплина',
            ]),
            'chart_soul' => $this->makeChart('Духовность и творчество', $indicator, [
                'soul_practices' => 'Практики',
                'soul_creativity' => 'Творчество',
                'soul_knowledge' => 'Знания',
                'soul_people' => 'Люди',
                'soul_nature' => 'Природа',
                'soul_reflection' => 'Размышления',
            ]),
            'chart_rest' => $this->makeChart('Отдых и хобби', $indicator, [
                'rest_passive' => 'Пассивный',
                'rest_active' => 'Активный',
                'rest_hobbies' => 'Хобби',
                'rest_social' => 'Общение',
                'rest_learning' => 'Обучение',
                'rest_relaxation' => 'Расслабление',
            ]),
            'chart_friends' => $this->makeChart('Друзья и окружение', $indicator, [
                'friends_support' => 'Поддержка',
                'friends_meetings' => 'Встречи',
                'friends_culture' => 'Культура',
                'friends_interests' => 'Интересы',
                'friends_trust' => 'Доверие',
                'friends_variety' => 'Разнообразие',
            ]),
            'chart_family' => $this->makeChart('Семья', $indicator, [
                'family_emotion' => 'Эмоции',
                'family_time' => 'Время',
                'family_rituals' => 'Ритуалы',
                'family_communication' => 'Коммуникация',
                'family_support' => 'Поддержка',
                'family_health' => 'Здоровье',
            ]),
            'chart_health' => $this->makeChart('Здоровье', $indicator, [
                'health_physical' => 'Физическое',
                'health_emotional' => 'Эмоциональное',
                'health_social' => 'Социальное',
                'health_sleep' => 'Сон',
                'health_prevention' => 'Профилактика',
                'health_balance' => 'Баланс',
            ]),
        ];
    }

    protected function averageFields($model, array $fields): float
    {
        $values = collect($fields)->map(fn($field) => (float) $model->$field);
        return round($values->avg(), 1);
    }


    protected function makeChart(string $title, $model, array $fields): array
    {
        return [
            'title' => $title,
            'labels' => array_values($fields),
            'values' => array_map(fn($field) => (int) $model->$field, array_keys($fields)),
            'colors' => collect(range(0, count($fields) - 1))->map(fn() => $this->randomColor())->toArray()
        ];
    }

    protected function randomColor(): string
    {
        return sprintf('#%06X', mt_rand(0, 0xFFFFFF));
    }

    protected function getViewData(): array
    {
        return [
            'summary' => $this->summary,
            'charts' => $this->charts,
        ];
    }
}
