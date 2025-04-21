<?php

namespace App\Filament\Widgets;

use App\Models\Task;
use Filament\Widgets\Widget;
use Guava\Calendar\Widgets\CalendarWidget;
use Illuminate\Support\Collection;
use Guava\Calendar\ValueObjects\CalendarEvent;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Filament\Actions\Action;
use Livewire\WithPagination;
use Livewire\Component;
use Filament\Support\RawJs;

class MyCalendar extends CalendarWidget
{
    public string $calendarView = 'timeGridWeek';

    // КНОПКИ СМЕНЫ РЕЖИМА КАЛЕНДАРЯ
    public function getHeaderActions(): array
    {
        return [
            Action::make('day')
                ->label('День')
                ->action(function () {
                    $this->calendarView = 'timeGridDay';
                })
                ->color(fn () => $this->calendarView === 'dayGridDay' ? 'primary' : 'gray'),

            Action::make('week')
                ->label('Неделя')
                ->action(function () {
                    $this->calendarView = 'timeGridWeek';
                })
                ->color(fn () => $this->calendarView === 'timeGridWeek' ? 'primary' : 'gray'),

            Action::make('month')
                ->label('Месяц')
                ->action(function () {
                    $this->calendarView = 'dayGridMonth';
                })
                ->color(fn () => $this->calendarView === 'dayGridMonth' ? 'primary' : 'gray'),
        ];
    }

    // ЗАПРОС ЗАДАЧ ИЗ БД
    public function getEvents(array $fetchInfo = []): Collection
    {
        return Task::query()
            ->where('user_id', Auth::id())
            ->whereNotNull('start_at')
            ->whereNotNull('deadline_at')
            ->get()
            ->map(function (Task $task) {
                $isOverdue = !$task->is_done && Carbon::parse($task->deadline_at)->isPast();
                $isDone = (bool) $task->is_done;

                // Определяем цвет по статусу
                $backgroundColor = match (true) {
                    $isDone => '#22c55e',    // ✅ Зелёный — если выполнено
                    $isOverdue => '#ef4444', // 🔴 Красный — если просрочено и не выполнено
                    default => '#3b82f6',    // 🔵 Синий — остальное
                };

                return CalendarEvent::make()
                    ->title($task->name)
                    ->start($task->start_at)
                    ->end($task->deadline_at) // важно указать end, даже если он такой же как start
                    ->display('auto') // <— добавлено, чтобы текст показывался в timeGrid
                    ->styles([
                        'background-color' => $backgroundColor,
                        'color' => '#ffffff',
                    ]);
            });
    }

    // Отображать шкалу времени начиная с первой задачи
    public function getOptions(): array
    {
        return [
            'slotMinTime' => $this->getEarliestTaskTime(),
        ];
    }

    // Ищем саммую раннюю задачу
    protected function getEarliestTaskTime(): string
    {
        $earliest = Task::query()
            ->where('user_id', Auth::id())
            ->whereDate('start_at', now()->toDateString())
            ->orderBy('start_at')
            ->value('start_at');

        return $earliest
            ? Carbon::parse($earliest)->format('H:i:s')
            : '05:00:00'; // fallback, если задач нет
    }

    // Триггер на смену вида календаря
    protected function getViewData(): array
    {
        return [
            'calendarKey' => md5($this->calendarView), // при смене режима виджет получит новый ключ
        ];
    }

    public function getLocale() : string
    {
       return 'ru';
    }
}
