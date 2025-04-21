<?php

namespace App\Filament\Pages;

use App\Models\MainKanban;
use App\Enums\KanBanStatus;
use App\Models\KanbanCategories;
use Illuminate\Support\Carbon;
use Mokhosh\FilamentKanban\Pages\KanbanBoard;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Filament\Forms;
use Filament\Actions\CreateAction;
use Filament\Tables;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;

class MainKanbanBoard extends KanbanBoard
{

    protected static string $recordTitleAttribute = 'name';
    protected static string $recordStatusAttribute = 'kanban_status';

    protected static string $model = MainKanban::class;
    protected static string $statusEnum = KanBanStatus::class;

    protected static ?string $navigationGroup = 'Планирование';
    protected static ?string $navigationIcon = 'heroicon-o-code-bracket-square';
    protected static ?string $navigationLabel = 'Мои дела';
    protected static ?string $title = 'Мои дела';
    protected static ?int $navigationSort = 2;
    // модалка
    protected string $editModalTitle = 'Изменить';
    protected string $editModalWidth = 'full';
    protected string $editModalSaveButtonLabel = 'Сохранить';
    protected string $editModalCancelButtonLabel = 'Закрыть';
    protected bool $editModalSlideOver = true;
    // фильтры
    public ?string $dateField = 'planned_date';
    public ?string $from = null;
    public ?string $to = null;

    // Переопределение статусов
    public function statuses(): Collection
    {
        return collect([
            ['id' => 'todo', 'title' => 'Нужно сделать'],
            ['id' => 'planned', 'title' => 'Запланировано'],
            ['id' => 'processi', 'title' => 'В процессе (я)'],
            ['id' => 'processo', 'title' => 'В процессе (другие)'],
            ['id' => 'today', 'title' => 'Сегодня'],
            ['id' => 'done', 'title' => 'Завершено']
        ]);
    }

    protected function records(): Collection
    {
        $query = MainKanban::query()->where('user_id', Auth::id());

        $field = request('date_field', 'planned_date');
        $from = request('from');
        $to = request('to');

        if ($from && $to) {
            $query->whereBetween($field, [
                Carbon::parse($from)->startOfDay(),
                Carbon::parse($to)->endOfDay(),
            ]);
        }
        return $query->get();
    }

    //ФИЛЬТРЫ
    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Widgets\MainKanbanFilters::class,
        ];
    }

    // КНОПКА СОЗДАТЬ ЗАПИСЬ
    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->model(MainKanban::class)
                ->label('Создать дело')
                ->icon('heroicon-m-plus-circle')
                ->closeModalByClickingAway(false)
                ->modalWidth('full')
                ->modalHeading('Новая запись')
                ->slideOver()
                ->form($this->mainForm()),
        ];
    }

    // СОЗДАНИЕ ЗАПИСИ
    protected function getEditModalFormSchema(null|int|string $recordId): array
    {
        return $this->mainForm($recordId);
    }

    /**
     * @param int|null $recordId
     * @return array
     */
    private function mainForm(null|int $recordId = null): array
    {
        return [
            Grid::make(12)->schema([
                // Левый столбец (30%)
                Section::make('Информация о деле')
                    ->columnSpan(4)
                    ->schema([
                        Hidden::make('user_id')->default(Auth()->id()),

                        // 1. Название (на всю ширину)
                        Grid::make(1)->schema([
                            TextInput::make('name')
                                ->label('Название дела')
                                ->required(),
                        ]),

                        // 2. Статус и Категория (по 50%)
                        Grid::make(2)->schema([
                            Select::make('kanban_status')
                                ->label('Статус')
                                ->required()
                                ->options(\App\Enums\KanBanStatus::options()),

                            Select::make('category_id')
                                ->label('Категория')
                                ->required()
                                ->searchable()
                                ->options(fn() => \App\Models\KanbanCategories::where('user_id', Auth::id())
                                    ->pluck('name', 'id')
                                    ->toArray()),
                        ]),

                        // 3. Стоимость и Оплачено (по 50%)
                        Grid::make(3)->schema([
                            TextInput::make('amount')
                                ->label('Стоимость')
                                ->numeric()
                                ->default('0')
                                ->postfix('₽')
                                ->live(onBlur: true)
                                ->afterStateUpdated(fn(Get $get, Set $set) => $set('remainder', (float)$get('amount') - (float)$get('amount_coll'))),

                            TextInput::make('amount_coll')
                                ->label('Оплачено')
                                ->numeric()
                                ->default('0')
                                ->postfix('₽')
                                ->live(onBlur: true)
                                ->afterStateUpdated(fn(Get $get, Set $set) => $set('remainder', (float)$get('amount') - (float)$get('amount_coll'))),

                            // 4. Остаток (расчет)
                            TextInput::make('remainder')
                                ->label('Остаток')
                                ->readOnly()
                                ->numeric()
                                ->suffix('₽'),
                            ]),

                        // 5. Плановая дата и Дедлайн (по 50%)
                        Grid::make(2)->schema([
                            DatePicker::make('planned_date')
                                ->label('Плановая дата')
                                ->native(false)
                                ->displayFormat('d.m.Y')
                                ->weekStartsOnMonday()
                                ->closeOnDateSelection(),

                            DatePicker::make('deadline_date')
                                ->label('Дедлайн')
                                ->native(false)
                                ->displayFormat('d.m.Y')
                                ->weekStartsOnMonday()
                                ->closeOnDateSelection(),
                        ]),

                        // 6. Описание (полностью)
                        Grid::make(1)->schema([
                            Textarea::make('description')
                                ->label('Заметка про дело')
                                ->rows(2),
                        ]),

                        Hidden::make('sortban_status')->default('todo'),
                    ]),


                // Правый столбец (70%)
                Section::make('Задачи по делу')
                    ->columnSpan(8)
                    ->schema([
                        Hidden::make('progress')->default(0),

                        Repeater::make('tasks')
                            ->relationship('tasks')
                            ->label('')
                            ->minItems(1)
                            ->required()
                            ->addActionLabel('Добавить задачу')
                            ->collapsible()
                            ->collapsed()
                            ->relationship()
                            ->cloneable()
                            ->afterStateUpdated(function (Get $get, Set $set, array $state) {
                                $done = 0;
                                $count = count($state);
                                foreach ($state as $item) {
                                    if ($item['is_done']) {
                                        $done++;
                                    }
                                }
                                $progress = $count > 0 ? $done / $count * 100 : 0;
                                $set('progress', round($progress));
                            })
                            ->itemLabel(function (array $state) {
                                if (empty($state)) {
                                    return 'Новая задача';
                                }

                                $msg = $state['is_done'] ? '✅' : '⌛️';
                                return $msg . ' ' . $state['name'];
                            })
                            ->columns(4)
                            ->orderColumn('order_column')
                            ->schema([
                                Hidden::make('user_id')->default(Auth()->id()),

                                ToggleButtons::make('is_done')
                                    ->label('Выполнено')
                                    ->default(false)
                                    ->inline()
                                    ->boolean()
                                    ->columnSpan(1)
                                    ->live(),

                                TextInput::make('name')
                                    ->label('Название')
                                    ->required()
                                    ->validationMessages([
                                        'required' => 'Необходимо ввести задачу внутри дела',
                                    ]),

                                DateTimePicker::make('start_at')
                                    ->native(false)
                                    ->displayFormat('d.m.Y H:i')
                                    ->seconds(false)
                                    ->weekStartsOnMonday()
                                    ->closeOnDateSelection()
                                    ->label('Когда и со скольки'),

                                DateTimePicker::make('deadline_at')
                                    ->native(false)
                                    ->displayFormat('d.m.Y H:i')
                                    ->seconds(false)
                                    ->weekStartsOnMonday()
                                    ->closeOnDateSelection()
                                    ->label('Дедлайн и во сколько'),
                            ]),
                    ]),
            ]),
        ];
    }


}
