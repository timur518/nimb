<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use App\Services\ChatGPTService;
use Illuminate\Support\Facades\Cache;
use Parsedown;
class ChatGptAnalyse extends Widget
{
    protected static string $view = 'filament.widgets.chat-gpt-analyse';
    public string $recommendation = '';

    public function mount(): void
    {
        $this->generateRecommendation();
    }

    protected function getViewData(): array
    {
        return [
            'recommendation' => $this->recommendation,
        ];
    }

    // Запрос к чату gpt
    public function generateRecommendation(): void
    {
        $indicator = \App\Models\BalanceIndicator::where('user_id', auth()->id())->first();
        $this->recommendation = $indicator?->gpt_analyse ?? 'Анализ пока не сгенерирован.';
        $this->recommendation = (new \Parsedown())->text($this->recommendation);
    }
}
