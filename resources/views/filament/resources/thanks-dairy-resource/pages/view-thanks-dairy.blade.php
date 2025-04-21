<x-filament::page>
    <div class="w-full py-12 px-8 bg-gradient-to-br from-blue-50 to-indigo-50">
        <div class="max-w-full mx-auto space-y-8">

            <!-- Заголовок страницы -->
            <h1 class="text-left text-4xl font-extrabold text-indigo-900 tracking-tight mb-6">
                📔 Запись от {{ \Carbon\Carbon::parse($record->date)->format('d.m.Y') }}
            </h1>

            <!-- Раздел благодарности -->
            @if($record->thanks)
                <section class="space-y-4">
                    <h2 class="text-2xl font-semibold text-indigo-600">🙏 Благодарю</h2>
                    <div class="bg-white p-6 rounded-lg shadow-lg">
                        <p class="text-lg text-gray-700 leading-relaxed whitespace-pre-line">{{ $record->thanks }}</p>
                    </div>
                </section>
            @endif

            <!-- Раздел принятия -->
            @if($record->acceptance)
                <section class="space-y-4">
                    <h2 class="text-2xl font-semibold text-teal-600">💫 Принимаю</h2>
                    <div class="bg-white p-6 rounded-lg shadow-lg">
                        <p class="text-lg text-gray-700 leading-relaxed whitespace-pre-line">{{ $record->acceptance }}</p>
                    </div>
                </section>
            @endif

            <!-- Раздел создания -->
            @if($record->creation)
                <section class="space-y-4">
                    <h2 class="text-2xl font-semibold text-rose-600">🔥 Создаю</h2>
                    <div class="bg-white p-6 rounded-lg shadow-lg">
                        <p class="text-lg text-gray-700 leading-relaxed whitespace-pre-line">{{ $record->creation }}</p>
                    </div>
                </section>
            @endif

            <!-- Раздел заметки -->
            @if($record->note)
                <section class="space-y-4">
                    <h2 class="text-2xl font-semibold text-gray-600">📝 Заметка</h2>
                    <div class="bg-white p-6 rounded-lg shadow-lg">
                        <p class="text-lg text-gray-700 leading-relaxed whitespace-pre-line italic">{{ $record->note }}</p>
                    </div>
                </section>
            @endif

            <!-- Ссылка на возвращение к дневнику -->
            <div class="text-left pt-2">
                <a href="{{ \App\Filament\Resources\ThanksDairyResource::getUrl() }}"
                   class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                    ← Вернуться к дневнику
                </a>
            </div>
        </div>
    </div>
</x-filament::page>
