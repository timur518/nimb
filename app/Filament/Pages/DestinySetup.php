<?php

namespace App\Filament\Pages;

use App\Models\Destiny;
use Filament\Actions\Action;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Actions;
use Filament\Notifications\Notification;
use App\Filament\Widgets\ChatGptDestinyAnalyse;

class DestinySetup extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-star';
    protected static ?string $navigationLabel = 'Предназначение';
    protected static ?string $navigationGroup = 'Настройки';
    protected static ?string $title = 'Предназначение и миссия';
    protected static string $view = 'filament.pages.destiny-setup';
    protected static ?int $navigationSort = 1;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(
            $this->getUserDestiny()?->toArray() ?? []
        );
    }

    public function getHeaderActions(): array
    {
        return [
            Action::make('day')
                ->label('Пройти тест')
                ->icon('heroicon-o-queue-list')
                ->url('https://ikigaitest.com/ru/lichnostnyy-test/')
                ->openUrlInNewTab(),
            ];
    }

    // Вывод ChatGPT анализа
    protected function getHeaderWidgets(): array
    {
        return [
            ChatGptDestinyAnalyse::class,
        ];
    }


    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Определи баланс между внутренним и внешним')
                    ->description('Почему важно знать все эти составляющие?
Знание своих миссии, страсти, призвания, профессии и предназначения помогает человеку жить осознанно, в гармонии с собой и миром, объединяя внутренние желания с внешними возможностями. Это позволяет не просто работать ради денег или существовать по инерции, а чувствовать смысл в каждом дне, быть полезным обществу, реализовывать свой потенциал и при этом чувствовать радость и удовлетворение от жизни.')
                    ->schema([
                        Textarea::make('mission')
                            ->label('Моя миссия — то, что ты любишь и в чем нуждается мир')
                            ->placeholder('Твоя миссия — как ты можешь изменить мир к лучшему.
Зачем знать: она даёт тебе ощущение значимости, направления и вдохновения.')
                            ->rows(3)
                            ->required(),

                        Textarea::make('vocation')
                            ->label('Мое призвание — то что требуется миру(!) и за что тебе могут платить')
                            ->placeholder('Это про то, как твои навыки и интересы могут быть полезны другим.
Зачем знать: помогает соединить смысл и материальное благополучие.')
                            ->rows(3)
                            ->required(),

                        Textarea::make('passion')
                            ->label('Моя страсть — то, что ты любишь и в чём ты хорош')
                            ->placeholder('Это то, что зажигает внутри. То, от чего кайфуешь.
Зачем знать: помогает не выгорать, даёт энергию и внутреннюю мотивацию.')
                            ->rows(3)
                            ->required(),

                        Textarea::make('profession')
                            ->label('Моя профессия — то, в чём ты хорош(!) и за что тебе платят')
                            ->placeholder('Это про конкретные навыки и умения.
Зачем знать: она даёт стабильность и позволяет применять твои способности.')
                            ->rows(3)
                            ->required(),

                        Textarea::make('destiny')
                            ->label('Мое предназначение')
                            ->placeholder('Это точка, где пересекаются страсть, миссия, профессия и призвание.
Зачем знать: это как внутренняя компас-навигация по жизни. Даёт ощущение гармонии и глубокой удовлетворённости.')
                            ->rows(3)
                            ->required(),

                        Hidden::make('gpt_analyse'),

                        Actions::make([
                            \Filament\Forms\Components\Actions\Action::make('save')
                                ->label('Сохранить и проанализировать')
                                ->action('save')
                                ->button()
                                ->extraAttributes([
                                    'class' => 'w-full mt-4 text-lg',
                                ]),
                        ])->alignEnd(),
                    ])
                    ->columns(2)
                    ->columnSpanFull()
                    ->extraAttributes([
                        'class' => 'bg-white shadow-lg rounded-xl p-2 sm:p-6',
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $destiny = $this->getUserDestiny();

        // Проверка заполненности
        $requiredFields = ['mission', 'vocation', 'passion', 'profession', 'destiny'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                Notification::make()
                    ->title('Заполните все поля, чтобы получить анализ от Нимба')
                    ->warning()
                    ->send();
                return;
            }
        }

        // Формируем промпт
        $prompt = "На основе результатов по Икигай-тесту, выведи: 1. мнение обо мне в одном абзаце, 2. Дай вдохновляющую и практичную рекомендацию, как мне реализовать свой потенциал, 3. На что важнее всего мне обращать внимание:\n" .
            "Моя миссия: {$data['mission']}\n" .
            "Мое призвание: {$data['vocation']}\n" .
            "Моя страсть: {$data['passion']}\n" .
            "Моя профессия: {$data['profession']}\n" .
            "Мое предназначение: {$data['destiny']}\n\n";

        // Генерация через ChatGPT
        $chatGPT = new \App\Services\ChatGPTService();
        $response = $chatGPT->ask($prompt);

        // Убираем лишние переноса строк из ответа
        $response = preg_replace('/\n+/', "\n", $response);
        // Преобразуем Markdown в HTML
        $gpt_analyse = (new \Parsedown())->text($response);
        $data['gpt_analyse'] = $gpt_analyse;

        if ($destiny) {
            $destiny->update($data);
            Notification::make()
                ->title('Данные обновлены, анализ проведен. Обновите страницу!')
                ->success()
                ->send();
        } else {
            $data['user_id'] = auth()->id();
            Destiny::create($data);
            Notification::make()
                ->title('Данные сохранены, анализ проведен. Обновите страницу!')
                ->success()
                ->send();
        }
    }

    protected function getUserDestiny(): ?Destiny
    {
        return Destiny::where('user_id', auth()->id())->first();
    }

}
