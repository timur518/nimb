<?php

namespace App\Filament\Pages;

use App\Models\Goals;
use App\Enums\GoalsBanStatus;
use Mokhosh\FilamentKanban\Pages\KanbanBoard;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\{Grid, Section, Hidden, TextInput, Select, Textarea, DatePicker};
use Filament\Actions\CreateAction;
use App\Filament\Widgets\GoalStats;

class GoalsKanban extends KanbanBoard
{
    protected static string $recordTitleAttribute = 'name';
    protected static string $recordStatusAttribute = 'goal_status';
    protected static string $recordView = 'filament.pages.goals-kanban.goal-card';
    protected static string $model = Goals::class;
    protected static string $statusEnum = GoalsBanStatus::class;

    protected static ?string $navigationGroup = 'Планирование';
    protected static ?string $navigationIcon = 'heroicon-o-flag';
    protected static ?string $navigationLabel = 'Мои цели';
    protected static ?string $title = 'Мои цели';
    protected static ?int $navigationSort = 3;

    public function statuses(): Collection
    {
        return collect([
            ['id' => 'todo', 'title' => 'Мечта записана'],
            ['id' => 'inprocess', 'title' => 'В процессе достижения'],
            ['id' => 'done', 'title' => 'Успешно достигнуты'],
        ]);
    }

    protected function records(): Collection
    {
        return Goals::where('user_id', Auth::id())->get();
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->model(Goals::class)
                ->label('Создать цель')
                ->icon('heroicon-m-plus-circle')
                ->closeModalByClickingAway(false)
                ->modalWidth('full')
                ->modalHeading('Новая цель')
                ->slideOver()
                ->form($this->mainForm()),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            GoalStats::class,
        ];
    }


    protected function getEditModalFormSchema(null|int|string $recordId): array
    {
        return $this->mainForm($recordId);
    }

    private function mainForm(null|int $recordId = null): array
    {
        return [
            Grid::make(12)->schema([
                Section::make('Цель')
                    ->columns(3)
                    ->schema([
                        Hidden::make('user_id')
                            ->default(Auth()->id()),

                        TextInput::make('name')
                            ->label('Название')
                            ->required()
                            ->columnSpan(2),

                        Select::make('goal_status')
                            ->label('Статус')
                            ->required()
                            ->options(GoalsBanStatus::options()),

                        TextInput::make('amount')
                            ->label('Стоимость цели')
                            ->numeric()
                            ->default('0')
                            ->postfix('₽'),

                        TextInput::make('amount_coll')
                            ->label('Накоплено и отложено')
                            ->numeric()
                            ->default('0')
                            ->postfix('₽'),

                        DatePicker::make('deadline_date')
                            ->label('Дедлайн')
                            ->native(false)
                            ->displayFormat('d.m.Y')
                            ->weekStartsOnMonday()
                            ->closeOnDateSelection(),

                        TextInput::make('picture_url')
                            ->label('Ссылка на картинку цели')
                            ->columnSpanFull(),

                        Textarea::make('description')
                            ->label('Описание')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
            ])
        ];
    }
}
