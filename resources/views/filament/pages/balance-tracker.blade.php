<x-filament::page>
    {{-- 💡 Блок AI-рекомендаций --}}
    <div class="mb-6">
        @livewire(\App\Filament\Widgets\ChatGptAnalyse::class)
    </div>
    {{-- Основной график и суммарные значения в двух столбцах, одна строка --}}
    <div class="flex flex-col lg:flex-row gap-6">
        {{-- Основной график --}}
        <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow w-full lg:w-1/2 flex justify-center items-center">
            <div>
                <h2 class="text-xl font-bold mb-4 text-center text-gray-800 dark:text-white">Общее колесо баланса</h2>
                <canvas id="chart-overall" width="400" height="400"></canvas>
            </div>
        </div>

        {{-- Суммарные значения с прогресс-барами --}}
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow w-full lg:w-1/2">
            <h2 class="text-xl font-bold mb-4 text-gray-800 dark:text-white">Суммарные значения</h2>
            <div class="space-y-5">
                @php
                    $colors = ['#4ade80', '#60a5fa', '#facc15', '#f472b6', '#34d399', '#a78bfa', '#fb7185', '#f97316'];
                    $i = 0;
                @endphp

                @foreach ($summary as $label => $value)
                    @php
                        $percentage = min(10, round($value))*10;
                        $color = $colors[$i % count($colors)];
                        $i++;
                    @endphp
                    <div style="margin-bottom:10px!important;">
                        <div class="flex justify-between mb-1">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-400">{{ $label }}</span>
                            <span class="text-sm text-gray-500 dark:text-gray-400">{{ $value }}/10</span>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-4 overflow-hidden">
                            <div class="h-4 text-xs text-white text-right pr-2 flex items-center justify-center" style="width: {{ $percentage }}%; background-color: {{ $color }};">{{ $value }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Остальные графики по категориям --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-10">
        @foreach([
            'Финансы' => 'chart_finance',
            'Карьера' => 'chart_career',
            'Саморазвитие' => 'chart_self',
            'Духовность и творчество' => 'chart_soul',
            'Отдых и хобби' => 'chart_rest',
            'Друзья и окружение' => 'chart_friends',
            'Семья' => 'chart_family',
            'Здоровье' => 'chart_health',
        ] as $title => $id)
            <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow">
                <h3 class="text-lg font-semibold mb-2 text-gray-800 dark:text-white">{{ $title }}</h3>
                <canvas id="{{ $id }}" width="300" height="300"></canvas>
            </div>
        @endforeach
    </div>

    {{-- Chart.js CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    {{-- Скрипты для всех графиков --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctxMain = document.getElementById('chart-overall').getContext('2d');
            new Chart(ctxMain, {
                type: 'polarArea',
                data: {
                    labels: @json(array_keys($summary)),
                    datasets: [{
                        label: '@json(array_keys($summary))',
                        data: @json(array_values($summary)),
                        backgroundColor: [
                            '#4ade80', '#60a5fa', '#facc15', '#f472b6',
                            '#34d399', '#a78bfa', '#fb7185', '#f97316'
                        ],
                    }],
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { position: 'right' }
                    }
                }
            });

            const charts = {
                chart_finance: @json($charts['chart_finance']),
                chart_career: @json($charts['chart_career']),
                chart_self: @json($charts['chart_self']),
                chart_soul: @json($charts['chart_soul']),
                chart_rest: @json($charts['chart_rest']),
                chart_friends: @json($charts['chart_friends']),
                chart_family: @json($charts['chart_family']),
                chart_health: @json($charts['chart_health']),
            };

            for (const [id, chart] of Object.entries(charts)) {
                const ctx = document.getElementById(id)?.getContext('2d');
                if (ctx) {
                    new Chart(ctx, {
                        type: 'polarArea',
                        data: {
                            labels: chart.labels,
                            datasets: [{
                                label: '',
                                data: chart.values,
                                backgroundColor: chart.colors,
                            }],
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: { position: 'bottom' }
                            }
                        }
                    });
                }
            }
        });
    </script>
</x-filament::page>
