<script>
    function onStart() {
        setTimeout(() => document.body.classList.add("grabbing"))
    }

    function onEnd() {
        document.body.classList.remove("grabbing")
    }

    function setData(dataTransfer, el) {
        dataTransfer.setData('id', el.id)
    }

    function onAdd(e) {
        const recordId = e.item.id
        const status = e.to.dataset.statusId
        const fromOrderedIds = [].slice.call(e.from.children).map(child => child.id)
        const toOrderedIds = [].slice.call(e.to.children).map(child => child.id)

        Livewire.dispatch('status-changed', {recordId, status, fromOrderedIds, toOrderedIds})
    }

    function onUpdate(e) {
        const recordId = e.item.id
        const status = e.from.dataset.statusId
        const orderedIds = [].slice.call(e.from.children).map(child => child.id)

        Livewire.dispatch('sort-changed', {recordId, status, orderedIds})
    }

    document.addEventListener('livewire:navigated', () => {
        const statuses = @js($statuses->map(fn ($status) => $status['id']))

        statuses.forEach(status => Sortable.create(document.querySelector(`[data-status-id='${status}']`), {
            group: 'filament-kanban',
            ghostClass: 'opacity-50',
            animation: 150,

            onStart,
            onEnd,
            onUpdate,
            setData,
            onAdd,
        }))
    })
</script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Получаем параметры из URL
            const urlParams = new URLSearchParams(window.location.search);
            const dateField = urlParams.get('date_field') || 'planned_date';
            const from = urlParams.get('from');
            const to = urlParams.get('to');

            // Если есть оба параметра "from" и "to", показываем уведомление
            if (from && to) {
                const fromDate = new Date(from);
                const toDate = new Date(to);
                const dateFieldLabel = dateField === 'planned_date' ? 'запланированной дате' : 'дедлайну';

                const notificationMessage = `Показаны записи по ${dateFieldLabel} с ${fromDate.toLocaleDateString()} по ${toDate.toLocaleDateString()}.`;

                // Создаем уведомление
                const notification = document.createElement('div');
                notification.classList.add('alert', 'alert-info', 'mb-4');
                notification.innerText = notificationMessage;

                // Добавляем уведомление в DOM
                const kanbanContainer = document.querySelector('.filament-kanban-container');
                if (kanbanContainer) {
                    kanbanContainer.prepend(notification);  // Помещаем уведомление в начало контейнера
                }
            }
        });
    </script>
