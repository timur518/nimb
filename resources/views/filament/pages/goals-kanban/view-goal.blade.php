<x-filament::page>
    <div class="rounded-xl overflow-hidden bg-white dark:bg-gray-800 shadow-md">
        @if ($this->goal->picture_url)
            <img src="{{ $this->goal->picture_url }}" alt="{{ $this->goal->name }}"
                 class="w-full object-cover object-center" style="height: 200px!important;">
        @endif

        <div class="p-6 space-y-4">
            <h1 class="text-2xl font-semibold text-gray-800 dark:text-white">
                {{ $this->goal->name }}
            </h1>

            @if (!empty($this->goal->description))
                <p class="text-gray-700 dark:text-gray-300">
                    {{ $this->goal->description }}
                </p>
            @endif

            @php
                $progress = $this->goal->amount > 0
                    ? min(100, round(($this->goal->amount_coll / $this->goal->amount) * 100))
                    : 0;
            @endphp

            @if ($this->goal->amount > 1)
                <div class="flex items-center justify-between gap-2">
                    <div class="w-full bg-gray-200 rounded-full h-4 dark:bg-gray-700">
                        <div
                            class="bg-blue-600 text-xs text-white text-center h-4 leading-none rounded-full transition-all duration-300"
                            style="width: {{ $progress }}%; background-color: #2563eb;">
                        </div>
                    </div>
                    <span class="text-sm font-medium text-gray-600 dark:text-gray-300">{{ $progress }}%</span>
                </div>
            @endif

            <div class="text-sm text-gray-600 dark:text-gray-300 space-y-1">
                <div><strong>Сумма цели:</strong> {{ number_format($this->goal->amount, 0, '', ' ') }} ₽</div>
                <div><strong>Накоплено:</strong> {{ number_format($this->goal->amount_coll, 0, '', ' ') }} ₽</div>

                @if ($this->goal->deadline_date)
                    <div><strong>Дедлайн:</strong> {{ $this->goal->deadline_date->format('d.m.Y') }}</div>
                @else
                    <div><strong>Дедлайн:</strong> Не указан</div>
                @endif

                <div><strong>Статус:</strong> {{ $this->goal->sortban_status->name ?? 'не указан' }}</div>
            </div>

            <div class="pt-4">
                <a href="{{ url()->previous() }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-white text-sm font-medium rounded-md hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                    ← Назад
                </a>
            </div>
        </div>
    </div>
</x-filament::page>
