<?php

namespace App\Filament\Pages;
use Filament\Pages\Page;
use App\Models\Goals;

class ViewGoal extends Page
{
    public ?Goals $goal = null;

    protected static string $view = 'filament.pages.goals-kanban.view-goal';

    protected static ?string $slug = 'goals/{record}';

    public function mount($record): void
    {
        $this->goal = Goals::findOrFail($record);
    }
}
