<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\GoalStats;
use App\Models\Goals;
use Filament\Pages\Page;
use App\Models\BalanceIndicator;
use App\Models\Destiny;
use Illuminate\Support\Facades\Auth;
use App\Models\MainKanban;
use App\Filament\Widgets\KanbanStats;
use App\Models\Task;
use Illuminate\Support\Carbon;

class PersonalMetrics extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-user-circle';
    protected static string $view = 'filament.pages.personal-metrics';
    protected static ?string $title = 'Самоанализ';

    protected function getHeaderWidgets(): array
    {
        return [
            KanbanStats::class,
        ];
    }

    public function getViewData(): array
    {
        $user = Auth::user();
        // Информация для главного колеса баланса и сводки к нему
        $indicator = BalanceIndicator::where('user_id', $user->id)->first();
        if ($indicator) {
            $summary = [
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
        } else {
           $summary = [
                    'Финансы' => 1,
                    'Карьера' => 2,
                    'Саморазвитие' => 3,
                    'Духовность и творчество' => 4,
                    'Отдых и хобби' => 5,
                    'Друзья и окружение' => 6,
                    'Семья' => 7,
                    'Здоровье' => 8,
                ];
        }

        // Задачи на сегодня и на завтра
        $today = Carbon::today();
        $tomorrow = Carbon::tomorrow();

        $tasksToday = Task::whereDate('deadline_at', $today)
            ->where('is_done', 0)
            ->where('user_id', $user->id)
            ->get();

        $tasksTomorrow = Task::whereDate('deadline_at', $tomorrow)
            ->where('is_done', 0)
            ->where('user_id', $user->id)
            ->get();

        // Статистика соответствие миссии
        $sortban = MainKanban::where('user_id', Auth::id())->get();
        if ($sortban->count() > 1) {
            $totaliwant = $sortban->where('sortban_status', 'i_want')->count();
            $totaltheywant = $sortban->where('sortban_status', 'they_want')->count();
            $sortresult = ($totaliwant / ($totaliwant + $totaltheywant)) * 100;
            $SortBanResults = [
                'totalIWant' => $totaliwant,
                'totalTheyWant' => $totaltheywant,
                'sortResult' => $sortresult,
            ];
        } else {
            $SortBanResults = [
                'totalIWant' => 0,
                'totalTheyWant' => 0,
                'sortResult' => 0,
                ];
        }

        // Статистика целей
        $goals = Goals::where('user_id', Auth::id())->get();
        $goalsStats = [
            'totalGoals' => $goals->count(),
            'totalAmount' => number_format($goals->sum('amount'), 0, '', ' '),

            'InProcessGoals' => $goals->where('goal_status', 'inprocess')->count(),
            'InProcessAmount' => number_format($goals->where('goal_status', 'inprocess')->sum('amount_coll'), 0, '', ' '),

            'doneGoals' => $goals->where('goal_status', 'done')->count(),
            'doneGoalsAmount' => number_format($goals->where('goal_status', 'done')->sum('amount'), 0, '', ' '),
        ];

        // предназначения
        $destinies = Destiny::where('user_id', $user->id)->first();
        if(!$destinies) {
            $destinies = (object) [
                'mission' => 'Не заполнено',
                'vocation' => 'Не заполнено',
                'passion' => 'Не заполнено',
                'profession' => 'Не заполнено',
                'destiny' => 'Не заполнено',
            ];
        }

        // ОТПРАВКА ДАННЫХ В ШАБЛОН ДАШБОРДА
        return [
            'tasksToday' => $tasksToday,
            'tasksTomorrow' => $tasksTomorrow,
            'summary' => $summary,
            'sortban' => $SortBanResults,
            'goals' => $goalsStats,
            'destinies' => $destinies,
        ];
    }

    protected function averageFields($model, array $fields): float
    {
        $values = collect($fields)->map(fn($field) => (float) $model->$field);
        return round($values->avg(), 1);
    }
}
