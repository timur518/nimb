<?php

namespace App\Filament\Pages;

use App\Models\BalanceIndicator;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Notifications\Notification;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use App\Services\ChatGPTService;

class BalanceIndicatorsSetup extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-pencil';
    protected static ?string $navigationLabel = 'Мои показатели';
    protected static ?string $navigationGroup = 'Колесо баланса';
    protected static ?string $title = 'Мои показатели';
    protected static string $view = 'filament.pages.balance-indicators-setup';
    protected ?string $subheading = 'Общее колесо баланса формируется на основании оценок в каждой сфере жизни. Неспешно оцени каждую сферу жизни от 0 до 10. Важно заполнить оценки для каждой сферы, для того чтобы иметь возможность ознакомиться с анализом на странице Трекер баланса.';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(
            $this->getUserIndicators()?->toArray() ?? []
        );
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                $this->section('Финансы', [
                    'finance_goals' => 'Финансовые цели',
                    'finance_learning' => 'Обучение',
                    'finance_environment' => 'Окружение',
                    'finance_tracking' => 'Учет финансов',
                    'finance_saving' => 'Сохранение',
                    'finance_income' => 'Доходы',
                    'finance_economy' => 'Экономия',
                    'finance_investment' => 'Инвестиции',
                ]),
                $this->section('Карьера', [
                    'career_growth' => 'Профессиональное развитие',
                    'career_engagement' => 'Взаимодействие с миром',
                    'career_environment' => 'Рабочая среда',
                    'career_balance' => 'Баланс работы/жизни',
                    'career_rewards' => 'Финансовое вознаграждение',
                    'career_goals' => 'Цели и амбиции',
                    'career_satisfaction' => 'Удовлетворение работой',
                ]),
                $this->section('Саморазвитие', [
                    'self_education' => 'Образование и знания',
                    'self_growth' => 'Личностный рост',
                    'self_skills' => 'Навыки и умения',
                    'self_creativity' => 'Креативность и самовыражение',
                    'self_social' => 'Социализация',
                    'self_planning' => 'Цели и планирование',
                    'self_discipline' => 'Дисциплина',
                ]),
                $this->section('Духовность и творчество', [
                    'soul_practices' => 'Духовные практики',
                    'soul_creativity' => 'Творческое самовыражение',
                    'soul_knowledge' => 'Знания и изучение',
                    'soul_people' => 'Единомышленники',
                    'soul_nature' => 'Природа и окружающий мир',
                    'soul_reflection' => 'Рефлексия и самоанализ',
                ]),
                $this->section('Отдых и хобби', [
                    'rest_passive' => 'Пассивный отдых',
                    'rest_active' => 'Активный отдых',
                    'rest_hobbies' => 'Хобби и увлечения',
                    'rest_social' => 'Социальные активности',
                    'rest_learning' => 'Обучение и развитие',
                    'rest_relaxation' => 'Психологическое расслабление',
                ]),
                $this->section('Друзья и окружение', [
                    'friends_support' => 'Взаимопомощь и поддержка',
                    'friends_meetings' => 'Общение и встречи',
                    'friends_culture' => 'Культурные мероприятия',
                    'friends_interests' => 'Общие интересы и увлечения',
                    'friends_trust' => 'Доверие и открытость',
                    'friends_variety' => 'Разнообразие окружения',
                ]),
                $this->section('Семья', [
                    'family_emotion' => 'Эмоциональная поддержка',
                    'family_time' => 'Совместное времяпровождение',
                    'family_rituals' => 'Общие традиции и ритуалы',
                    'family_communication' => 'Общение',
                    'family_support' => 'Поддержка в трудных ситуациях',
                    'family_health' => 'Здоровье и благополучие',
                ]),
                $this->section('Здоровье', [
                    'health_physical' => 'Физическое здоровье',
                    'health_emotional' => 'Эмоциональное здоровье',
                    'health_social' => 'Социальное здоровье',
                    'health_sleep' => 'Здоровый сон',
                    'health_prevention' => 'Профилактика заболеваний',
                    'health_balance' => 'Баланс работы и отдыха',
                ]),

                Actions::make([
                    Action::make('save')
                        ->label('Сохранить')
                        ->action('save')
                        ->color('primary')
                        ->icon('heroicon-m-check'),
                ])->alignEnd(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $record = $this->getUserIndicators();

        if ($record) {
            $record->update($data);
        } else {
            $data['user_id'] = auth()->id();
            $record = BalanceIndicator::create($data);
        }

        $user = auth()->user();
        $indicator = \App\Models\BalanceIndicator::where('user_id', $user->id)->first();

        if (!$indicator) {
            $this->recommendation = 'Нет данных для проведения анализа.';
            return;
        }

        // Получаем список всех полей модели, которые отвечают за оценки
        $indicatorData = $indicator->toArray();

        // Исключаем системные поля (если нужно)
        $exclude = ['id', 'user_id', 'created_at', 'updated_at', 'gpt_analyse'];
        $values = collect($indicatorData)->except($exclude);

        // Проверка на пустые значения
        if ($values->contains(function ($value) {
            return is_null($value);
        })) {
            $this->recommendation = 'Недостаточно данных для анализа. Установите оценки для ВСЕХ сфер жизни.';
            return;
        }

        $categories = [
            'Финансы' => [
                'finance_goals' => 'Цели',
                'finance_learning' => 'Обучение',
                'finance_environment' => 'Окружение',
                'finance_tracking' => 'Отслеживание',
                'finance_saving' => 'Сбережения',
                'finance_income' => 'Доход',
                'finance_economy' => 'Экономия',
                'finance_investment' => 'Инвестиции',
            ],
            'Карьера' => [
                'career_growth' => 'Карьерный рост',
                'career_engagement' => 'Вовлеченность',
                'career_environment' => 'Окружение',
                'career_balance' => 'Баланс',
                'career_rewards' => 'Награды',
                'career_goals' => 'Цели',
                'career_satisfaction' => 'Удовлетворенность',
            ],
            'Саморазвитие' => [
                'self_education' => 'Образование',
                'self_growth' => 'Рост',
                'self_skills' => 'Навыки',
                'self_creativity' => 'Творчество',
                'self_social' => 'Общение',
                'self_planning' => 'Планирование',
                'self_discipline' => 'Дисциплина',
            ],
            'Духовность и творчество' => [
                'soul_practices' => 'Практики',
                'soul_creativity' => 'Творчество',
                'soul_knowledge' => 'Знания',
                'soul_people' => 'Люди',
                'soul_nature' => 'Природа',
                'soul_reflection' => 'Размышления',
            ],
            'Отдых и хобби' => [
                'rest_passive' => 'Пассивный',
                'rest_active' => 'Активный',
                'rest_hobbies' => 'Хобби',
                'rest_social' => 'Общение',
                'rest_learning' => 'Обучение',
                'rest_relaxation' => 'Расслабление',
            ],
            'Друзья и окружение' => [
                'friends_support' => 'Поддержка',
                'friends_meetings' => 'Встречи',
                'friends_culture' => 'Культура',
                'friends_interests' => 'Интересы',
                'friends_trust' => 'Доверие',
                'friends_variety' => 'Разнообразие',
            ],
            'Семья' => [
                'family_emotion' => 'Эмоции',
                'family_time' => 'Время',
                'family_rituals' => 'Ритуалы',
                'family_communication' => 'Коммуникация',
                'family_support' => 'Поддержка',
                'family_health' => 'Здоровье',
            ],
            'Здоровье' => [
                'health_physical' => 'Физическое',
                'health_emotional' => 'Эмоциональное',
                'health_social' => 'Социальное',
                'health_sleep' => 'Сон',
                'health_prevention' => 'Профилактика',
                'health_balance' => 'Баланс',
            ],
        ];

        $text = "Я оценил свои показатели колеса баланса так:\n\n";

        foreach ($categories as $category => $fields) {
            $text .= "🔹 **{$category}**\n";
            foreach ($fields as $field => $label) {
                $value = $indicator->$field ?? 0;
                $text .= "- {$label}: {$value}/10\n";
            }
            $text .= "\n";
        }

        $text .= "На основе этих данных:\n";
        $text .= "1. Дай анализ показателей в одном абзаце.\n";
        $text .= "2. Объясни как проседающие показатели могут влиять на меня в одном абзаце.\n";
        $text .= "3. Сформируй мини-план улучшения проседающих сфер (в виде маркированного списка).\n";
        $text .= "Не задавай вопросов в ответе и не используй вводные вроде 'конечно' или 'как ИИ'.";


        // Отправка в ChatGPT
        $chatGPT = new \App\Services\ChatGPTService();
        $response = $chatGPT->ask($text);

        // Убираем лишние переноса строк из ответа
        $response = preg_replace('/\n+/', "\n", $response);
        // Преобразуем Markdown в HTML
        $analysis = (new \Parsedown())->text($response);
        $record->update(['gpt_analyse' => $analysis]);

        Notification::make()
            ->title('Данные сохранены, анализ проведен!')
            ->success()
            ->send();
    }


    protected function getUserIndicators(): ?BalanceIndicator
    {
        return BalanceIndicator::where('user_id', auth()->id())->first();
    }

    protected function ratingField(string $name, string $label): Select
    {
        return Select::make($name)
            ->label($label)
            ->options(collect(range(0, 10))->mapWithKeys(fn($i) => [$i => $i])->toArray())
            ->default(0);
    }

    protected function section(string $title, array $fields): Section
    {
        return Section::make($title)
            ->schema(
                collect($fields)->map(fn($label, $name) => $this->ratingField($name, $label))->toArray()
            )
            ->columns(2);
    }
}
