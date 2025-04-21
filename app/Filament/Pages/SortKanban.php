<?php

namespace App\Filament\Pages;

use App\Models\MainKanban;
use App\Enums\SortBanStatus;
use App\Models\KanbanCategories;
use Filament\Pages\Dashboard\Concerns\HasFilters;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Mokhosh\FilamentKanban\Pages\KanbanBoard;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Filament\Forms;
use Filament\Actions\CreateAction;
use Filament\Tables;
use Filament\Widgets\Widget;
use Illuminate\Support\Carbon;
use Filament\Forms\Form;
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

class SortKanban extends KanbanBoard
{
    protected static string $recordTitleAttribute = 'name';

    use HasFilters;
    protected static string $recordStatusAttribute = 'sortban_status';

    protected static string $model = MainKanban::class;
    protected static string $statusEnum = SortBanStatus::class;

    protected static ?string $navigationGroup = 'Планирование';
    protected static ?string $navigationIcon = 'heroicon-o-funnel';
    protected static ?string $navigationLabel = 'Фильтр дел';
    protected static ?string $title = 'Сортировочный канбан';
    protected static ?int $navigationSort = 1;
    protected ?string $subheading = 'Выгрузите максимальное количество дел, хранящихся в вашей голове. Чем больше - тем лучше, а лучше - это когда в голове не осталось ни одного дела, которое не было бы записано в Нимб.';


    protected string $editModalTitle = 'Изменить';
    protected string $editModalWidth = 'full';
    protected string $editModalSaveButtonLabel = 'Сохранить';
    protected string $editModalCancelButtonLabel = 'Закрыть';
    protected bool $editModalSlideOver = true;

    public ?string $dateField = 'planned_date';
    public ?string $from = null;
    public ?string $to = null;

    // Переопределение статусов
    public function statuses(): Collection
    {
        return collect([
            ['id' => 'todo', 'title' => 'Нужно сделать'],
            ['id' => 'i_want', 'title' => 'Я хочу'],
            ['id' => 'they_want', 'title' => 'Я должен'],
        ]);
    }
    protected function records(): Collection
    {
        $query = MainKanban::query()->where('user_id', Auth::id());

        // Получаем параметры из URL
        $from = request()->query('from');
        $to = request()->query('to');
        $field = request()->query('date_field', 'planned_date');

        $this->filters['from'] = $from;
        $this->filters['to'] = $to;
        $this->filters['date_field'] = $field;

        // Валидируем поле
        $allowedFields = ['planned_date', 'deadline_date'];
        if (!in_array($field, $allowedFields)) {
            $field = 'planned_date';
        }

        // Если есть from и to — фильтруем по указанному полю
        if ($from && $to) {
            $query->whereBetween($field, [
                Carbon::parse($from)->startOfDay(),
                Carbon::parse($to)->endOfDay(),
            ]);
        }
        // Если нет никаких query-параметров — возвращаем все записи
        elseif (count(request()->query()) === 0) {
            // никаких фильтров
        }
        // Если есть параметры, но не from/to — возвращаем пустую коллекцию
        else {
            return collect();
        }
        return $query->get();
    }

    //ФИЛЬТРЫ
    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Widgets\KanbanFilters::class,
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
                    ->columnSpan([
                        'default' => 12,
                        'lg' => 4,
                    ])
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
                            Select::make('sortban_status')
                                ->label('Статус')
                                ->required()
                                ->options(\App\Enums\SortBanStatus::options()),

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

                        Hidden::make('kanban_status')->default('todo'),
                    ]),
                // Правый столбец (70%)
                Section::make('Задачи по делу')
                    ->columnSpan([
                        'default' => 12,
                        'lg' => 8,
                    ])
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
                                    ->label('Название задачи')
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
                            ])->columns([
                                'xl' => 4,
                                'lg' => 2,
                                'md' => 2,
                            ]),
                    ]),
            ]),
        ];
    }

}
