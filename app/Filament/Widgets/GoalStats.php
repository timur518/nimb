<?php
namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Goals;
use Illuminate\Support\Facades\Auth;

class GoalStats extends BaseWidget
{
    protected function getStats(): array
    {
        $goals = Goals::where('user_id', Auth::id())->get();
        $total = $goals->sum('amount');
        $collected = $goals->sum('amount_coll');
        $remaining = $total - $collected;
        $done = $goals->where('goal_status', 'done')->sum('amount');

        return [
            Stat::make('Сумма всех целей', number_format($total, 0, '', ' ') . ' ₽')
            ->icon('heroicon-o-banknotes'),
            Stat::make('Отложено на цели', number_format($collected, 0, '', ' ') . ' ₽')
            ->icon('heroicon-o-circle-stack'),
            Stat::make('Осталось накопить', number_format($remaining, 0, '', ' ') . ' ₽')
            ->icon('heroicon-o-rocket-launch'),
            Stat::make('Сумма достигнутых целей', number_format($done, 0, '', ' ') . ' ₽')
            ->icon('heroicon-o-check-circle'),
        ];
    }
}
