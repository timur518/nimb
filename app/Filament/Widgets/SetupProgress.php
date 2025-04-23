<?php
namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Collection;
use App\Models\{Destiny, BalanceIndicator, Goals, KanbanCategories, MainKanban, ThanksDairy};
use Filament\Notifications\Notification;
use App\Models\User;

class SetupProgress extends Widget
{
    protected static string $view = 'filament.widgets.setup-progress';

    protected int | string | array $columnSpan = 'full';
    public int $progress = 0;
    public Collection $steps;

    // Если настройка завершена — не показывать виджет вообще
    public static function canView(): bool
    {
        return ! auth()->user()->setup_completed;
    }

    //Получить шаги настройки
    public function getSteps(): Collection
    {
        $user = auth()->user();

        return collect([
            [
                'title' => 'Пройди тест "Икигай"',
                'url' => route('filament.admin.pages.destiny-setup'),
                'completed' => Destiny::where('user_id', Auth()->id())->exists(),
            ],
            [
                'title' => 'Сформируй колесо баланса',
                'url' => route('filament.admin.pages.balance-indicators-setup'),
                'completed' => BalanceIndicator::where('user_id', Auth()->id())->exists(),
            ],
            [
                'title' => 'Создай 10 целей',
                'url' => route('filament.admin.pages.goals-kanban'),
                'completed' => Goals::where('user_id', Auth()->id())->count() >= 10,
            ],
            [
                'title' => 'Создай категорию дел',
                'url' => route('filament.admin.resources.kanban-categories.index'),
                'completed' => KanbanCategories::where('user_id', Auth()->id())->exists(),
            ],
            [
                'title' => 'Создай 10 дел и отфильтруй их',
                'url' => route('filament.admin.pages.sort-kanban'),
                'completed' => MainKanban::where('user_id', Auth()->id())->count() >= 10,
            ],
            [
                'title' => 'Запиши благодарность в дневник',
                'url' => route('filament.admin.resources.thanks-dairies.index'),
                'completed' => ThanksDairy::where('user_id', Auth()->id())->exists(),
            ],
        ]);
    }

    // Сформировать прогресс
    public function getProgress(): int
    {
        $steps = $this->getSteps();
        $completed = $steps->where('completed', true)->count();
        return intval(($completed / $steps->count()) * 100);
    }

    public function mount()
    {
        $progress = $this->getProgress();
        $this->steps = collect(); // сначала пустая коллекция
        $this->steps = $this->getSteps();
        $this->progress = $this->getProgress();

        if ($this->progress >= 100 && !auth()->user()->setup_completed) {
            auth()->user()->update([
                'setup_completed' => true,
            ]);
        }

        if ($progress < 100) {
            Notification::make()
                ->title("Настройка Нимба: {$progress}% завершено")
                ->success()
                ->persistent()
                ->send();
        }
    }

    public function getData(): array
    {
        return [
            'steps' => $this->getSteps(),
            'progress' => $this->getProgress(),
        ];
    }
}

