<x-filament::widget>
    <x-filament::card>
        <div class="space-y-4">
            {{ $this->form }}

            @if ($this->getFilterHint())
                <p class="text-sm">
                    {{ $this->getFilterHint() }}
                </p>
            @endif
        </div>
    </x-filament::card>
</x-filament::widget>
