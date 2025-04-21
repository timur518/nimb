<div id="{{ $record->getKey() }}"
     wire:click="recordClicked('{{ $record->getKey() }}', {{ @json_encode($record) }})"
     class="record cursor-grab rounded-xl overflow-hidden bg-white dark:bg-gray-600 shadow-md"
     @if($record->timestamps && now()->diffInSeconds($record->{$record::UPDATED_AT}) < 3)
         x-data
     x-init="
            $el.classList.add('animate-pulse-twice', 'bg-primary-100', 'dark:bg-primary-800')
            $el.classList.remove('bg-white', 'dark:bg-gray-800')
            setTimeout(() => {
                $el.classList.remove('bg-primary-100', 'dark:bg-primary-800')
                $el.classList.add('bg-white', 'dark:bg-gray-800')
            }, 3000)
        "
    @endif
>
    <a href="/goals/{{$record->id}}" class="block rounded-xl overflow-hidden bg-white dark:bg-gray-800 shadow-md hover:shadow-lg transition-shadow duration-200">
    @if ($record->picture_url)
        <img src="{{ $record->picture_url }}" alt="{{ $record->name }}"
             class="w-full object-cover object-center" style="height:150px!important">
    @endif

    <div class="p-4 space-y-3 text-gray-800 dark:text-white">
        <h3 class="text-lg font-semibold">
            {{ $record->name }}
        </h3>

        @php
            $progress = $record->amount > 0
                ? min(100, round(($record->amount_coll / $record->amount) * 100))
                : 0;
        @endphp

        @if ($record->amount > 1)
            <div class="flex items-center justify-between gap-2 relative">
                <div class="w-full bg-gray-200 rounded-full h-2 dark:bg-gray-700">
                    <div class="bg-blue-600 text-xs font-medium text-blue-100 text-center h-2 leading-none rounded-full"
                         style="background-color:#2563eb; width: {{ $progress }}%;">
                        &nbsp;
                    </div>
                </div>
            </div>
        @endif

        <div class="text-md text-gray-600 dark:text-white space-y-1">
            <div><strong>Сумма цели:</strong> {{ number_format($record->amount, 0, '', ' ') }} ₽</div>
            <div><strong>Накоплено:</strong> {{ number_format($record->amount_coll, 0, '', ' ') }} ₽</div>
        </div>
    </div>

    </a>
</div>
