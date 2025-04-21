
@extends('layouts.master')

@section('content')
    <div class="container mt-4">
        <!-- Блок для вывода сообщений об ошибках/успехе -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Информация о сделке -->
        <div class="card mt-3 mb-4">
            <div class="card-header">
                Просмотр сделки
            </div>
            <div class="card-body">
                <div class="row" style="justify-content: space-between;">
                    <!-- Левая часть: Основная информация -->
                    <div class="col-md-6">
                        <div class="info-card">
                            <div class="info-card-header">
                                <h3>Информация о сделке</h3>
                            </div>
                            <div class="info-card-body">
                                <!-- ID сделки -->
                                <div class="info-item">
                                    <span class="info-icon">🆔</span>
                                    <p><strong>ID сделки:</strong> {{ $deal->id }}</p>
                                </div>

                                <!-- Предмет -->
                                <div class="info-item">
                                    <span class="info-icon">📄</span>
                                    <p><strong>Предмет:</strong>
                                        <a href="/{{ $deal->post->slug }}/{{ $deal->post->id }}" target="_blank" class="info-link">
                                            {{ $deal->post->title }}
                                        </a>
                                    </p>
                                </div>

                                <!-- Исполнитель -->
                                <div class="info-item">
                                    <span class="info-icon">👨‍💻</span>
                                    <p><strong>Исполнитель:</strong> {{ $deal->seller->name }}</p>
                                </div>

                                <!-- Покупатель -->
                                <div class="info-item">
                                    <span class="info-icon">👤</span>
                                    <p><strong>Покупатель:</strong> {{ $deal->buyer->name }}</p>
                                </div>

                                <!-- Назначенное время -->
                                <div class="info-item">
                                    <span class="info-icon">⏰</span>
                                    <p><strong>Назначенное время:</strong> {{ $deal->desired_time }}</p>
                                </div>

                                <!-- VIN номер -->
                                <div class="info-item">
                                    <span class="info-icon">🚗</span>
                                    <p><strong>VIN номер:</strong> {{ $deal->vin_number }}</p>
                                </div>

                                <!-- Информация об устройстве -->
                                @if($deal->device)
                                    <div class="info-item">
                                        <span class="info-icon">💻</span>
                                        <p><strong>Устройство:</strong> {{ $deal->device->serial_number }} ({{ $deal->device->device_type }})</p>
                                    </div>

                                    <!-- Статус VM -->
                                    <div class="info-item">
                                        <span class="info-icon">🔄</span>
                                        <p><strong>Статус {{ $deal->device->device_type }}:</strong> {{ $vmStatus }}</p>
                                    </div>

                                    <!-- Кнопка активации RDBox -->
                                    @if($deal->status == 'Ожидание выполнения' && auth()->id() == $deal->seller_id && $vmStatus != "Работает")
                                        <div class="info-item">
                                            <form action="{{ route('deal.activate_rdbox', $deal->id) }}" method="post">
                                                @csrf
                                                <button type="submit" class="copyable-text btn-activate">
                                                    <span class="btn-icon">🚀</span> Активировать {{ $deal->device->device_type }}
                                                </button>
                                            </form>
                                        </div>
                                    @elseif($vmStatus == "Работает")

                                        <div class="info-item">
                                            <div id="ip-port" class="copyable-text" onclick="copyToClipboard()" title="Кликните, чтобы скопировать">
                                                <span class="info-icon">🌐</span>
                                                <p><strong>IP для подключения:</strong>
                                                    <span class="ip-address">{{ $deal->device->device_ip}}:{{$deal->device->user_port }}</span>
                                                </p>
                                            </div>
                                        </div>


                                        @if($deal->started_at)
                                            <div class="info-item">
                                                <p><strong>Работа выполняется:</strong> {{ now()->diffInMinutes($deal->started_at) }} минут</p>
                                            </div>
                                        @endif
                                    @endif
                                @else
                                    <div class="info-item">
                                        <span class="info-icon">❌</span>
                                        <p><strong>Устройство:</strong> Не указано</p>
                                    </div>
                                @endif

                                @if($deal->started_at && $deal->completed_at)
                                    <div class="info-item">
                                        <p><strong>Время выполнения:</strong>
                                            {{ \Carbon\Carbon::parse($deal->completed_at)->diffInMinutes(\Carbon\Carbon::parse($deal->started_at)) }} минут
                                        </p>
                                    </div>
                                @endif

                                <!-- Кнопка для перехода в диалог -->
                                @if($threadId)
                                    <a href="{{ url('account/messages/' . $threadId) }}" class="btn btn-primary mt-3">
                                        Перейти в диалог
                                    </a>
                                @else
                                    <p class="mt-3">Диалог не найден</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Правая часть: Сумма и статус -->
                    <div class="col-md-5 text-end">
                        <div class="d-flex justify-content-between">
                            <p class="mb-3"><span class="badge bg-success fs-5">{{ $deal->deal_amount }} {{ config('settings.currency') }}₽</span></p>
                            <p class="mb-3">
                            <span class="badge bg-info fs-5">
                                @if($deal->status == 'created')
                                    Новая
                                @else
                                    {{ $deal->status }}
                                @endif
                            </span>
                            </p>
                        </div>

                        <!-- Кнопки действий -->
                        <div class="d-flex gap-2 justify-content-end">
                            @if(auth()->id() == $deal->seller_id)
                                @if($deal->status == 'created')
                                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#acceptDealModal">
                                        Принять сделку
                                    </button>
                                    <form action="{{ route('deal.reject', $deal->id) }}" method="post" class="d-inline-block">
                                        @csrf
                                        <button type="button" class="btn btn-danger" onclick="openRejectDealModal({{ $deal->id }})">Отклонить сделку</button>
                                    </form>
                                @elseif($deal->status == 'выполняется')
                                    <form action="{{ route('deal.request_cancel', $deal->id) }}" method="post" class="d-inline-block">
                                        @csrf
                                        <button type="button" class="btn btn-warning" onclick="openCancelPopup()">Запросить отмену</button>
                                    </form>
                                @endif
                            @elseif(auth()->id() == $deal->buyer_id)
                                <!-- Действия для покупателя -->
                                @if($deal->status == 'выполняется')
                                    <form action="{{ route('deal.request_cancel', $deal->id) }}" method="post" class="d-inline-block">
                                        @csrf
                                        <button type="button" class="btn btn-warning" onclick="openCancelPopup()">Запросить отмену</button>
                                    </form>

                                    <form id="completeDealForm" action="{{ route('deal.complete', $deal->id) }}" method="POST" class="d-inline-block">
                                        @csrf
                                        <button type="button" class="btn btn-primary" onclick="openCompletionModal({{ $deal->id }})">Принять выполнение</button>
                                    </form>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Блок с текстом, если сделка в статусе "запрос отмены" -->
                @if($deal->status == 'запрос отмены')
                    <hr>
                    <div class="mt-4">
                        <p><strong>Пользователь запросил отмену сделки по причине:</strong></p>
                        <p>{{ $deal->cancellation_reason }}</p>
                        <p><strong>Запрос будет рассмотрен модераторами площадки и в случае необходимости с Вами свяжутся для уточнения деталей сделки.</strong></p>
                    </div>
                @endif
            </div>
        </div>



        <!-- POPUP для отмены сделки -->
        <div id="cancelPopup" style="display:none;">
            <form action="{{ route('deal.request_cancel', $deal->id) }}" method="post">
                @csrf
                <label for="cancel_reason">Введите причину отмены:</label>
                <textarea name="cancel_reason" id="cancel_reason" class="form-control" rows="3" required></textarea>
                <button type="submit" class="btn btn-danger mt-2">Отправить запрос отмены</button>
                <button type="button" class="btn btn-secondary mt-2" onclick="closeCancelPopup()">Закрыть</button>
            </form>
        </div>

        <!-- Модальное окно для подтверждения -->
        <div class="modal fade" id="confirmCompletionModal" tabindex="-1" aria-labelledby="confirmCompletionModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="confirmCompletionModalLabel">Подтверждение завершения сделки</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Вы уверены, что хотите принять выполнение работ? Принимая сделку, вы соглашаетесь с тем, что Исполнитель качественно оказал вам услуги.
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                        <form id="confirmCompletionForm" action="" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-primary">Подтвердить</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Модальное окно подтверждения отклонения сделки -->
        <div class="modal fade" id="rejectDealModal" tabindex="-1" aria-labelledby="rejectDealModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="rejectDealModalLabel">Подтверждение отклонения сделки</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Вы уверены, что хотите отклонить сделку? Это действие нельзя отменить.
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                        <form id="rejectDealForm" action="" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-danger">Отклонить</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>


        <!-- Модальное окно для указания времени выполнения -->
        <div class="modal fade" id="acceptDealModal" tabindex="-1" aria-labelledby="acceptDealModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="acceptDealModalLabel">Подтверждение сделки</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="acceptDealForm" action="{{ route('deal.accept', $deal->id) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="desired_time" class="form-label">Желаемое время заказчика:</label>
                                <input type="text" class="form-control" value="{{ $deal->desired_time }}" disabled>
                            </div>
                            <div class="mb-3">
                                <label for="execution_time" class="form-label">Ваше время выполнения:</label>
                                <input type="datetime-local" class="form-control" id="execution_time" name="execution_time"
                                       value="{{ \Carbon\Carbon::parse($deal->desired_time)->format('Y-m-d\TH:i') }}" required>
                            </div>

                            <!-- Поле для выбора устройства исполнителя (только для Can-моста) -->
                            @if($deal->device && $deal->device->device_type === 'Can-мост')
                                <div class="mb-3">
                                    <label for="master_device_id" class="form-label">Выберите ваше устройство Can-мост:</label>
                                    <select class="form-control" id="master_device_id" name="master_device_id" required>
                                        <option value="">-- Выберите устройство --</option>
                                        @foreach(auth()->user()->devices->where('device_type', 'Can-мост') as $device)
                                            <option value="{{ $device->id }}">{{ $device->serial_number }}</option>
                                        @endforeach
                                    </select>
                                    @if(auth()->user()->devices->where('device_type', 'Can-мост')->count() === 0)
                                        <small class="text-danger">У вас нет устройств Can-мост. Вы не можете принять эту сделку.</small>
                                    @endif
                                </div>
                            @endif

                            <button type="submit" class="btn btn-primary"
                                    @if($deal->device && $deal->device->device_type === 'Can-мост' && auth()->user()->devices->where('device_type', 'Can-мост')->count() === 0) disabled @endif>
                                Подтвердить
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>

        /* Заголовок карточки */
        .info-card-header {
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }

        .info-card-header h3 {
            margin: 0;
            font-size: 24px;
            color: #333;
        }

        /* Элементы информации */
        .info-item {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            padding: 10px;
            border-radius: 8px;
            background: #f9f9f9;
            transition: background 0.3s ease;
        }



        /* Иконки */
        .info-icon {
            font-size: 20px;
            margin-right: 10px;
            color: #007bff;
        }

        /* Ссылки */
        .info-link {
            color: #007bff;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .info-link:hover {
            color: #0056b3;
            text-decoration: underline;
        }

        /* Кнопки */
        .btn-activate, .btn-dialog {
            display: flex;
            align-items: center;
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            transition: all 0.3s ease;
        }

        .btn-activate {
            color: #444;
            border: none;
        }

        .btn-activate:after {
            content:"" !important;
        }

        .btn-dialog {
            background: linear-gradient(135deg, #28a745, #00ff7f);
            color: white;
            border: none;
        }

        .btn-activate:hover, .btn-dialog:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .btn-icon {
            margin-right: 10px;
        }

        /* Стили для копируемого текста */
        .copyable-text {
            cursor: pointer;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #f9f9f9;
            display: flex;
            align-items: center;
            transition: all 0.3s ease;
            display:flex;
        }

        .copyable-text:hover {
            background-color: #e9f5ff;
            border-color: #007bff;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .copyable-text:active {
            transform: translateY(0);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .copyable-text .ip-address {
            font-weight: bold;
            color: #007bff;
            text-decoration: underline;
            text-decoration-style: dotted;
            text-underline-offset: 3px;
        }


        /* Стили для текста IP */
        .copyable-text .ip-address {
            font-weight: bold;
            color: #007bff; /* Синий цвет для выделения IP */
            text-decoration: underline;
            text-decoration-style: dotted; /* Пунктирное подчеркивание */
            text-underline-offset: 3px; /* Отступ подчеркивания */
        }

        /* Эффект при наведении */
        .copyable-text:hover {
            background-color: #e9f5ff; /* Легкий голубой фон при наведении */
            border-color: #007bff;
            transform: translateY(-2px); /* Легкий подъем */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Тень */
        }

        /* Эффект при клике */
        .copyable-text:active {
            transform: translateY(0); /* Убираем подъем при клике */
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); /* Уменьшаем тень */
        }

        /* Иконка копирования (опционально) */
        .copyable-text::after {
            content: "📋"; /* Иконка копирования */
            margin-left: 10px;
            font-size: 14px;
            opacity: 0.7;
            transition: opacity 0.3s ease;
        }

        .copyable-text:hover::after {
            opacity: 1; /* Показываем иконку при наведении */
        }
        /* Стили для всплывашки */
        .notification {
            position: fixed;
            bottom: 20px;
            right: 20px;
            padding: 15px 25px;
            border-radius: 5px;
            color: white;
            font-size: 14px;
            font-weight: bold;
            z-index: 1000;
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.3s ease, transform 0.3s ease;
        }

        .notification.success {
            background-color: #007bff; /* Зеленый цвет для успеха */
        }

        .notification.error {
            background-color: #dc3545; /* Красный цвет для ошибки */
        }

        /* Анимация появления */
        .notification.show {
            opacity: 1;
            transform: translateY(0);
        }
    </style>

    <script>
        function openCancelPopup() {
            document.getElementById('cancelPopup').style.display = 'block';
        }
        function closeCancelPopup() {
            document.getElementById('cancelPopup').style.display = 'none';
        }

        function openCompletionModal(dealId) {
            // Устанавливаем action формы в модальном окне
            const form = document.getElementById('confirmCompletionForm');
            form.action = `/deals/${dealId}/complete/`;

            // Открываем модальное окно
            const modal = new bootstrap.Modal(document.getElementById('confirmCompletionModal'));
            modal.show();
        }

        function openAcceptDealModal() {
            const modal = new bootstrap.Modal(document.getElementById('acceptDealModal'));
            modal.show();
        }

        function openRejectDealModal(dealId) {
            // Устанавливаем action формы отклонения
            const form = document.getElementById('rejectDealForm');
            form.action = `/deals/${dealId}/reject/`;

            // Открываем модальное окно
            const modal = new bootstrap.Modal(document.getElementById('rejectDealModal'));
            modal.show();
        }

        function copyToClipboard() {
            // Получаем текст из элемента
            const ipPortElement = document.getElementById('ip-port');
            const textToCopy = ipPortElement.innerText.replace('IP для подключения: ', '').trim();

            // Копируем текст в буфер обмена
            navigator.clipboard.writeText(textToCopy)
                .then(() => {
                    // Показываем всплывашку
                    showNotification('Скопировано в буфер обмена!');
                })
                .catch((err) => {
                    console.error('Ошибка при копировании: ', err);
                    showNotification('Не удалось скопировать', 'error');
                });
        }

        function showNotification(message, type = 'success') {
            // Создаем элемент для всплывашки
            const notification = document.createElement('div');
            notification.className = `notification ${type}`;
            notification.textContent = message;

            // Добавляем всплывашку в тело документа
            document.body.appendChild(notification);

            // Запускаем анимацию появления
            setTimeout(() => {
                notification.classList.add('show');
            }, 10);

            // Удаляем всплывашку через 3 секунды
            setTimeout(() => {
                notification.classList.remove('show');
                setTimeout(() => {
                    notification.remove();
                }, 300); // Ждем завершения анимации исчезновения
            }, 3000);
        }
    </script>
@endsection
