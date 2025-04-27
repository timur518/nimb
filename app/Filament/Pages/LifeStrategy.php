<?php

namespace App\Filament\Pages;

use App\Filament\Resources\LifePriorityResource\Widgets\LifePriorityTableWidget;
use App\Filament\Resources\LifeRuleResource\Widgets\LifeRuleTableWidget;
use App\Filament\Resources\ReminderResource\Widgets\ReminderTableWidget;
use App\Filament\Resources\ValuesResource\Widgets\ValuesTableWidget;
use App\Filament\Resources\FutureVisionResource\Widgets\FutureVisionTableWidget;
use Filament\Pages\Page;

class LifeStrategy extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text'; // Иконка для страницы
    protected static string $view = 'filament.pages.life-strategy'; // Указываем шаблон Blade
    protected static ?string $title = 'Стратегия жизни';

    // Метод для рендеринга страницы с указанием типа возвращаемого значения
    public function getHeaderWidgets(): array
    {
        return [
            ValuesTableWidget::class,     // Виджет для ценностей
            //FutureVisionTableWidget::class,   // Виджет для видений будущего
            //LifePriorityTableWidget::class,  // Виджет для жизненных приоритетов
            //LifeRuleTableWidget::class,      // Виджет для правил жизни
            //ReminderTableWidget::class,      // Виджет для напоминаний
        ];
    }

}
