<?php
namespace App\Services;

use OpenAI\Laravel\Facades\OpenAI;
use Parsedown;

class ChatGPTService
{
    public function ask(string $prompt, string $systemMessage = 'Ты психологический ассистент и тебя зовут "Нимб".'): string
    {
        $response = OpenAI::chat()->create([
            'model' => 'gpt-4o-mini', // или 'gpt-3.5-turbo' для экономии
            'messages' => [
                ['role' => 'system', 'content' => $systemMessage],
                ['role' => 'user', 'content' => $prompt],
            ],
        ]);
        $answer = $response->choices[0]->message->content;

        return $answer;
    }
}
