<?php
namespace App\Filament\Widgets;

use App\Models\Destiny;
use Filament\Widgets\Widget;

class ChatGptDestinyAnalyse extends Widget
{
    protected static string $view = 'filament.widgets.chat-gpt-destiny-analyse';

    public ?string $gptAnalysis = null;

    public function mount(): void
    {
        $this->gptAnalysis = Destiny::where('user_id', auth()->id())->value('gpt_analyse');
    }

    protected int | string | array $columnSpan = 'full';
}
