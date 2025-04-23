<x-filament::widget>
    <x-filament::card>
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-bold">Настройка Нимба</h2>
            <div class="text-sm text-gray-500">{{ $progress }}% завершено</div>
        </div>

        <div class="w-full bg-gray-200 rounded-full h-3 mb-4">
            <div class="bg-primary-600 h-3 rounded-full" style="width: {{ $progress }}%"></div>
        </div>

        <ul class="space-y-2">
            @if ($steps && $steps->count())
                <ul class="space-y-2">
                    @foreach ($steps as $step)
                        <li class="flex items-start gap-3 p-3 rounded-xl border border-gray-200 shadow-sm bg-white hover:bg-gray-50 transition">
                            <div>
                                @if ($step['completed'])
                                    <x-heroicon-o-check-circle class="w-6 h-6 mt-1" style="color:#22c55e;" />
                                @else
                                    <x-heroicon-o-information-circle class="w-6 h-6 text-gray-400 mt-1" />
                                @endif
                            </div>
                            <div>
                                <a href="{{ $step['url'] }}" class="text-base font-medium text-gray-800 dark:text-gray-800 hover:underline">
                                    {{ $step['title'] }}
                                </a>
                                @if (! $step['completed'])
                                    <a href="{{ $step['url'] }}">
                                    <p class="text-sm text-gray-500">Нажми, чтобы выполнить</p>
                                    </a>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-gray-500">Загрузка шагов...</p>
            @endif

        </ul>
    </x-filament::card>
</x-filament::widget>
