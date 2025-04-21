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
                Section::make('Запись в канбане')
                    ->columns(3)
                    ->schema([
                        Hidden::make('user_id')
                            ->default(Auth()->id()),

                        TextInput::make('name')
                            ->label('Название')
                            ->required()
                            ->columnSpan(2),

                        Select::make('sortban_status')
                            ->label('Статус фильтра')
                            ->required()
                            ->options(\App\Enums\SortBanStatus::options()),

                        Hidden::make('kanban_status')
                            ->default('todo'),

                        Select::make('category_id')
                            ->label('Категория')
                            ->required()
                            ->searchable()
                            ->options(fn () => \App\Models\KanbanCategories::where('user_id', Auth::id())
                                ->pluck('name', 'id')
                                ->toArray()),

                        TextInput::make('amount')
                            ->label('Стоимость')
                            ->numeric()
                            ->default('0')
                            ->postfix('₽')
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Get $get, Set $set) =>
                            $set('remainder', (float)$get('amount') - (float)$get('amount_coll'))
                            ),

                        TextInput::make('amount_coll')
                            ->label('Оплачено')
                            ->numeric()
                            ->default('0')
                            ->postfix('₽')
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Get $get, Set $set) =>
                            $set('remainder', (float)$get('amount') - (float)$get('amount_coll'))
                            ),

                        TextInput::make('remainder')
                            ->label('Остаток')
                            ->readOnly()
                            ->numeric()
                            ->suffix('₽'),

                        DatePicker::make('planned_date')
                            ->label('План')
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

                        Textarea::make('description')
                            ->label('Описание')
                            ->rows(2)
                            ->columnSpanFull(),
                    ]),

                Hidden::make('progress')->default(0),
                Section::make('Задачи')
                    ->schema([
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
//                                        $items = $get('../');
                                $done = 0;
                                $count = count($state);
                                foreach ($state as $item) {
                                    if ($item['is_done']) {
                                        $done++;
                                    }
                                }
                                if ($count) {$progress = $done / $count * 100;}
                                else $progress = 0;
                                $set('progress', round($progress));
                            })
                            ->itemLabel(function (array $state) {
                                if (empty($state)) {
                                    return 'Новая задача';
                                }

                                $msg = '';
                                if ($state['is_done']) {
                                    $msg .= '✅';
                                } else {
                                    $msg .= '⌛️';
                                }
                                return $msg . ' ' . $state['name'];
                            })
                            ->columns(3)
                            ->orderColumn('order_column')
                            ->schema([
                                Hidden::make('user_id')
                                    ->default(Auth()->id()),
                                ToggleButtons::make('is_done')
                                    ->label('Выполнено')
                                    ->default(false)
                                    ->inline()
                                    ->boolean()->columnSpan(1)
                                    ->live(),
                                TextInput::make('name')
                                    ->label('Название')
                                    ->required()
                                    ->validationMessages([
                                        'required' => 'Необходимо ввести задачу внутри дела',
                                    ]),
                                DatePicker::make('deadline_at')
                                    ->native(false)
                                    ->displayFormat('d.m.Y')
                                    ->weekStartsOnMonday()
                                    ->closeOnDateSelection()
                                    ->label('Срок выполнения'),
                            ])
                    ])
            ])
        ];
    }

}
