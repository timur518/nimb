<?php

namespace App\Filament\Widgets;

use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Pages\Dashboard\Concerns\HasFilters;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\Widget;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;

use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Illuminate\Http\Request;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;

class MainKanbanFilters extends Widget implements HasForms
{
    use InteractsWithForms;

    protected static bool $isLazy = false;

    protected static string $view = 'filament.widgets.main-kanban-filters';

    public ?string $date_field = null;
    public ?string $from = null;
    public ?string $to = null;

    public function mount(Request $request): void
    {
        $this->from = $request->from ? Carbon::parse($request->from) : null;
        $this->to = $request->to ? Carbon::parse($request->to) : null;
        $this->date_field = $request->date_field ?? 'planned_date';

        $this->form->fill([
            'date_field' => $this->date_field,
            'from' => $this->from,
            'to' => $this->to,
        ]);
    }


    public function getForm(string $name = null): Form
    {
        return Form::make($this)
            ->schema([
                Select::make('date_field')
                    ->label('')
                    ->prefix('Дата')
                    ->reactive()
                    ->live()
                    ->options([
                        'planned_date' => 'Плановая дата',
                        'deadline_date' => 'Дедлайн',
                    ]),

                DatePicker::make('from')
                    ->label('')
                    ->prefix('С')
                    ->displayFormat('d.m.Y')
                    ->reactive()
                    ->live()
                    ->native(true),

                DatePicker::make('to')
                    ->label('')
                    ->prefix('По')
                    ->displayFormat('d.m.Y')
                    ->reactive()
                    ->live()
                    ->native(true),

                Actions::make([
                    Action::make('apply')
                        ->label('Применить')
                        ->url(fn(Get $get) => route('filament.admin.pages.sort-kanban', array_filter([
                            'date_field' => $get('date_field'),
                            'from' => $get('from'),
                            'to' => $get('to'),
                        ])))
                        ->openUrlInNewTab(false),

                    Action::make('reset')
                        ->label('Сбросить')
                        ->url(route('filament.admin.pages.main-kanban-board'))
                        ->color('gray'),
                ])->fullWidth(),
            ])
            ->columns(5);
    }

    // Добавляем функцию для генерации подсказки
    public function getFilterHint(): string
    {
        $request = request(); // Получаем текущий запрос
        // Проверяем наличие параметров в URL
        $dateField = $request->date_field ?? 'planned_date';
        $from = $request->from ? Carbon::parse($request->from)->format('d.m.Y') : null;
        $to = $request->to ? Carbon::parse($request->to)->format('d.m.Y') : null;

        // Если есть все параметры, генерируем нужную подсказку
        if ($dateField && $from && $to) {
            $dateFieldLabel = $dateField === 'planned_date' ? 'запланированной дате' : 'дедлайну';
            return "→ Показаны записи по {$dateFieldLabel} с {$from} по {$to}.";
        }

        return '→ Показаны все записанные дела'; // Если параметров нет, не показываем подсказку
    }

    public function getColumnSpan(): int|string|array
    {
        return 'full';
    }
}
