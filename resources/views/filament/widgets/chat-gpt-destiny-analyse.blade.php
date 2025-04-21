<x-filament-widgets::widget>
            @if ($gptAnalysis)
                <div class="bg-white dark:bg-gray-900 shadow rounded-xl p-6">
                    <h2 class="text-xl font-bold mb-4 text-gray-800 dark:text-white">Анализ от Нимба</h2>
                    <p class="text-gray-700 dark:text-gray-300 whitespace-pre-line">{!! nl2br($gptAnalysis) !!}</p>
                </div>
            @endif
</x-filament-widgets::widget>
