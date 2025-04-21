<x-filament::page>

    {{-- Блок с таблицами задач --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-8">

        {{-- Задачи на сегодня --}}
        <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow rounded-xl">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold text-gray-800 dark:text-white">Задачи на сегодня</h2>
            </div>
            <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                <table class="table-auto w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700 text-sm font-medium text-gray-600 dark:text-gray-300">
                    <tr>
                        <th class="px-4 py-2 text-left dark:text-white" style="width:33%">Проект</th>
                        <th class="px-4 py-2 text-left dark:text-white" style="width:66%">Задача</th>
                    </tr>
                    </thead>
                    <tbody class="text-sm text-gray-800 dark:text-gray-100">
                    @forelse ($tasksToday as $index => $task)
                        <tr class="{{ $index % 2 === 0 ? 'bg-white dark:bg-gray-800' : 'bg-gray-50 dark:bg-gray-700' }}">
                            <td class="px-4 py-2">{{ $task->mainKanban->name ?? '—' }}</td>
                            <td class="px-4 py-2">{{ $task->name ?? 'Без названия' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="px-4 py-4 text-center text-gray-500 dark:text-gray-400">
                                Задач на сегодня нет 🎉
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Задачи на завтра --}}
        <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow rounded-xl">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold text-gray-800 dark:text-white">Задачи на завтра</h2>
            </div>
            <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                <table class="table-auto w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700 text-sm font-medium text-gray-600 dark:text-gray-300">
                    <tr>
                        <th class="px-4 py-2 text-left dark:text-white" style="width:33%">Проект</th>
                        <th class="px-4 py-2 text-left dark:text-white" style="width:66%">Задача</th>
                    </tr>
                    </thead>
                    <tbody class="text-sm text-gray-800 dark:text-gray-100">
                    @forelse ($tasksTomorrow as $index => $task)
                        <tr class="{{ $index % 2 === 0 ? 'bg-white dark:bg-gray-800' : 'bg-gray-50 dark:bg-gray-700' }}">
                            <td class="px-4 py-2">{{ $task->mainKanban->name ?? '—' }}</td>
                            <td class="px-4 py-2">{{ $task->name ?? 'Без названия' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="px-4 py-4 text-center text-gray-500 dark:text-gray-400">
                                Задач на завтра нет 🎉
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-3">
        {{-- Соответствие желаниям --}}
        <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow mt-0">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold text-gray-800 dark:text-white">Соответствие желаниям</h2>
            </div>

            {{-- Соответствие миссии --}}
            <div class="mb-6">
                <div class="flex justify-between mb-1">
                    <span class="text-sm font-medium text-gray-800 dark:text-gray-200">Соответствие миссии</span>
                    <span class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ $sortban['sortResult'] }}%</span>
                </div>
                <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-4">
                    <div
                        class="h-4 rounded-full transition-all duration-300"
                        style="width: {{ $sortban['sortResult'] }}%; background-color: {{ $sortban['sortResult'] > 50 ? '#86efac' : '#fca5a5' }};">
                    </div>
                </div>
            </div>
            <br>
            {{-- Я хочу / Я должен --}}
            <div class="grid grid-cols-2 gap-4">
                <!-- Я хочу -->
                <div class="bg-green-50 dark:bg-green-900 rounded-xl p-4 shadow-sm ring-1 ring-green-200 dark:ring-green-700">
                    <div class="text-sm font-medium text-green-800 dark:text-green-300 mb-1">Я хочу</div>
                    <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $sortban['totalIWant'] }}</div>
                </div>

                <!-- Я должен -->
                <div class="bg-red-50 dark:bg-red-900 rounded-xl p-4 shadow-sm ring-1 ring-red-200 dark:ring-red-700">
                    <div class="text-sm font-medium text-red-800 dark:text-red-300 mb-1">Я должен</div>
                    <div class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $sortban['totalTheyWant'] }}</div>
                </div>
            </div>
        </div>

        {{-- Мои цели --}}
        <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow mt-0">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold text-gray-800 dark:text-white">Мои цели</h2>
            </div>

            <ul class="space-y-3">
                <li class="flex justify-between border-b border-gray-200 dark:border-gray-700 pb-2">
                    <span class="text-gray-600 dark:text-gray-400">Всего целей:</span>
                    <span class="font-semibold text-gray-800 dark:text-white">{{$goals['totalGoals']}} целей на сумму {{$goals['totalAmount']}}&nbsp;₽</span>
                </li>
                <li class="flex justify-between border-b border-gray-200 dark:border-gray-700 pb-2">
                    <span class="text-gray-600 dark:text-gray-400">В процессе достижения:</span>
                    <span class="font-semibold text-gray-800 dark:text-white">{{$goals['InProcessGoals']}} целей пополнено на {{$goals['InProcessAmount']}}&nbsp;₽</span>
                </li>
                <li class="flex justify-between border-b border-gray-200 dark:border-gray-700 pb-2">
                    <span class="text-gray-600 dark:text-gray-400">Успешно достигнуто:</span>
                    <span class="font-semibold text-green-600 dark:text-green-400">{{$goals['doneGoals']}} целей на сумму {{$goals['doneGoalsAmount']}}&nbsp;₽</span>
                </li>
            </ul>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Основной график --}}
        <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow">
            <h2 class="text-xl font-bold text-gray-800 dark:text-white mb-4">Общее колесо баланса</h2>
            <canvas id="chart-overall" width="400" height="400"></canvas>
        </div>

        {{-- Значения для основного графика --}}
        <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow">
            <h2 class="text-xl font-bold text-gray-800 dark:text-white mb-4">Суммарные значения</h2>
            @foreach ($summary as $label => $value)
                <div class="mb-4">
                    <div class="text-gray-800 dark:text-gray-100">{{ $label }}: {{ $value }}</div>
                    <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-4 mt-2">
                        @php
                            $color = '#f1f1f1';
                            if ($value < 2) $color = '#f8d7da';
                            elseif ($value < 3) $color = '#f8c0a4';
                            elseif ($value < 4) $color = '#f9e29c';
                            elseif ($value < 5.5) $color = '#a3d8f4';
                            elseif ($value < 7.5) $color = '#a1e6a1';
                            else $color = '#66bb6a';
                        @endphp
                        <div class="h-4 rounded-full" style="background-color: {{ $color }}; width: {{ min($value * 10, 100) }}%;"></div>
                    </div>
                </div>
            @endforeach
        </div>

    </div>

    {{-- Мои смыслы --}}
    <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow mt-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold text-gray-800 dark:text-white">Мои смыслы</h2>
            <a href="/destiny-setup"><button class="text-blue-500 hover:text-blue-700 dark:text-blue-300 hover:dark:text-blue-400 flex items-center">
                <x-heroicon-o-pencil class="h-5 w-5 mr-2" /> Изменить
            </button></a>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 md:grid-cols-3 lg:grid-cols-4">
            @foreach (['mission' => 'Миссия', 'vocation' => 'Призвание', 'passion' => 'Страсть', 'profession' => 'Профессия', 'destiny' => 'Предназначение'] as $key => $label)
                <div class="bg-white dark:bg-gray-800 shadow rounded-xl p-6">
                    <h3 class="text-lg font-semibold mb-2 text-gray-800 dark:text-white">{{ $label }}</h3>
                    <p class="text-left text-gray-300 dark:text-gray-300 font-medium">{{ $destinies->$key ?? '—' }}</p>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Chart.js CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    {{-- Скрипт для отрисовки графиков --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const isDarkMode = document.documentElement.classList.contains('dark');

            const backgroundColor = isDarkMode ? 'rgba(72, 163, 226, 0.2)' : 'rgba(72, 163, 226, 0.3)';
            const borderColor = isDarkMode ? '#90cdf4' : '#48a3e2';
            const pointBackgroundColor = isDarkMode ? '#90cdf4' : '#48a3e2';
            const gridColor = isDarkMode ? '#4b5563' : '#d1d5db'; // gray-600 vs gray-300
            const angleLineColor = isDarkMode ? '#6b7280' : '#9ca3af'; // gray-500 vs gray-400
            const fontColor = isDarkMode ? '#f9fafb' : '#111827'; // gray-50 vs gray-900

            const ctxMain = document.getElementById('chart-overall').getContext('2d');
            new Chart(ctxMain, {
                type: 'radar',
                data: {
                    labels: @json(array_keys($summary)),
                    datasets: [{
                        label: ' ',
                        data: @json(array_values($summary)),
                        backgroundColor: backgroundColor,
                        borderColor: borderColor,
                        borderWidth: 2,
                        pointBackgroundColor: pointBackgroundColor,
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                        pointHoverRadius: 7,
                        pointHitRadius: 10,
                    }],
                },
                options: {
                    responsive: true,
                    scales: {
                        r: {
                            angleLines: {
                                color: angleLineColor,
                            },
                            grid: {
                                color: gridColor,
                            },
                            pointLabels: {
                                color: fontColor,
                                font: {
                                    size: 14,
                                    weight: '500'
                                }
                            },
                            suggestedMin: 0,
                            suggestedMax: 10,
                            ticks: {
                                color: fontColor,
                                backdropColor: 'transparent',
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'hidden',
                            labels: {
                                color: fontColor,
                                font: {
                                    size: 14,
                                    weight: 'bold',
                                },
                            }
                        },
                    },
                },
            });
        });
    </script>


</x-filament::page>
