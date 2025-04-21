<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class TasksCalendar extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $title = 'Календарь планирования';
    protected static string $view = 'filament.pages.tasks-calendar';

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Widgets\MyCalendar::class,
        ];
    }
}
