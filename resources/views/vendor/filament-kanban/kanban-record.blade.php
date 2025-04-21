<div id="{{ $record->getKey() }}"  wire:click="recordClicked('{{ $record->getKey() }}', {{ @json_encode($record) }})"
        class="record bg-white dark:bg-gray-700 text-gray-600 dark:text-gray-200 rounded-lg px-4 py-2 cursor-grab font-medium"
        @if($record->timestamps && now()->diffInSeconds($record->{$record::UPDATED_AT}) < 3)
            x-data
        x-init="
            $el.classList.add('animate-pulse-twice', 'bg-primary-100', 'dark:bg-primary-800')
            $el.classList.remove('bg-white', 'dark:bg-gray-700')
            setTimeout(() => {
                $el.classList.remove('bg-primary-100', 'dark:bg-primary-800')
                $el.classList.add('bg-white', 'dark:bg-gray-700')
            }, 3000)
        "
        @endif
>
    <div class="flex flex-col gap-4">
        <b>{{ $record->{static::$recordTitleAttribute} }}</b>

        @if ($record->amount > 1)
        <div class="flex items-center justify-between gap-2 relative">
            <div class="w-full bg-gray-200 rounded-full text-center h-2 dark:bg-white">
                <div class="bg-blue-600 h-2 text-xs font-medium text-blue-100 text-center leading-none rounded-full" style="background-color:#2563eb; @php echo 'width: ' . ($record->amount_coll / $record->amount)*100 . '%'; @endphp"> &nbsp;</div>
            </div>
        </div>
        @endif

        <div class="flex justify-between">
            <span
                    class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-sm font-medium text-green-700 ring-1 ring-inset ring-green-600/20">{{number_format($record->amount, 0, '', ' ')}} ₽</span>
            @if ($record->planned_date)
                <span class="inline-flex items-center rounded-md bg-gray-50 px-2 py-1 text-sm font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10"  title="Запланировано">{{ \Carbon\Carbon::parse($record->planned_date)->format('d.m.Y') }}</span>
            @else
                <span
                        class="inline-flex items-center rounded-md bg-gray-50 px-2 py-1 text-sm font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10"
                        title="Запланировано">Не запланировано</span>
            @endif
        </div>
        <div>
        @if(isset($record->category['name']))
                <span class="inline-flex items-center rounded-md bg-blue-200 px-2 py-1 text-sm font-medium text-blue-600 ring-1 ring-inset ring-blue-500/10">{{$record->category['name']}}</span>
        @endif

        @if ($record->deadline_date)
                <span class="inline-flex items-center rounded-md bg-red-200 px-2 py-1 text-sm font-medium text-red-600 ring-1 ring-inset ring-red-500/10"  title="Дедлайн">Срок: {{ \Carbon\Carbon::parse($record->deadline_date)->format('d.m.Y') }}</span>
        @else
                <span class="inline-flex items-center rounded-md bg-red-200 px-2 py-1 text-sm font-medium text-red-600 ring-1 ring-inset ring-red-500/10"  title="Дедлайн">Без срока</span>
        @endif
        </div>

    </div>

</div>
