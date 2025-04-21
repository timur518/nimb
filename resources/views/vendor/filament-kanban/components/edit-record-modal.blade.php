<x-filament-panels::form wire:submit.prevent="editModalFormSubmitted">
    <x-filament::modal id="kanban--edit-record-modal" :slideOver="$this->getEditModalSlideOver()" :width="$this->getEditModalWidth()">
        <x-slot name="header">
            <x-filament::modal.heading>
                Изменить запись
            </x-filament::modal.heading>
        </x-slot>

        {{ $this->form }}

        <x-slot name="footer">
            <x-filament::button type="submit">
                Сохранить
            </x-filament::button>

            <x-filament::button color="gray" x-on:click="isOpen = false">
                Отменить
            </x-filament::button>
        </x-slot>
    </x-filament::modal>
</x-filament-panels::form>
