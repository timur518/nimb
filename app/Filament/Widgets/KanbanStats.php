<?php
namespace App\Filament\Widgets;

use App\Models\MainKanban;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Goals;
use Illuminate\Support\Facades\Auth;

class KanbanStats extends BaseWidget
{
    protected function getStats(): array
    {
        $kanbans = MainKanban::where('user_id', Auth::id())->get();
        $total = $kanbans->count();
        $planned = $kanbans->count();
        $untouched = $kanbans->where('sortban_status', 'todo')->count();
        $done = $kanbans->where('kanban_status', 'done')->count();

        return [
            Stat::make('Всего дел', number_format($total, 0, '', ' '))
                ->icon('heroicon-o-bars-arrow-up'),
            Stat::make('Запланировано дел', number_format($planned, 0, '', ' '))
                ->icon('heroicon-o-calendar-date-range')
                ->color('warning'),
            Stat::make('Неразобрано', number_format($untouched, 0, '', ' '))
                ->icon('heroicon-o-bars-3-bottom-right')
                ->color('grey'),
            Stat::make('Всего выполнено', number_format($done, 0, '', ' '))
                ->icon('heroicon-o-document-check')
                ->color('success'),
        ];
    }
}
