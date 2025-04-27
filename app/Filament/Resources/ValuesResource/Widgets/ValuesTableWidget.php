<?php

namespace App\Filament\Resources\ValuesResource\Widgets;

use Filament\Tables;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\Value;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Hidden;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Contracts\Support\Htmlable;


class ValuesTableWidget extends BaseWidget
{
    protected int|string|array $columnSpan = 'full'; // чтобы виджет занимал всю ширину

    protected function getTableHeading(): string
    {
        return 'Мои ценности';
    }

    protected function getTableDescription(): string | Htmlable
    {
        return 'Сборник ваших главных жизненных ценностей. Например: Свобода — я остаюсь верным себе и всегда имею выбор.';
    }


    public function getTableEmptyStateHeading(): string
    { return 'Пока не записано ни одной ценности'; }


    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('sphere')
                ->label('Сфера')
                ->sortable()
                ->searchable(),
            Tables\Columns\TextColumn::make('value')
                ->label('Мои ценности')
                ->tooltip(fn(Value $record) => $record->value),
        ];
    }

    protected function getTableHeaderActions(): array
    {
        return [
            CreateAction::make('create')
                ->label('Добавить запись')
                ->form([
                    Hidden::make('user_id')
                    ->default(auth()->id()),
                    TextInput::make('sphere')
                        ->label('Сфера')
                        ->required()
                        ->maxLength(255),
                    TextArea::make('value')
                        ->label('Моя ценность')
                        ->required()
                        ->maxLength(255),
                ])
                ->modalHeading('Добавление новой ценности'),
        ];
    }

    // Добавляем действия для редактирования и удаления
    protected function getTableActions(): array
    {
        return [
            // Действие для редактирования
            EditAction::make()
                ->label('Редактировать')
                ->form([
                    TextInput::make('sphere')
                        ->label('Сфера')
                        ->required()
                        ->maxLength(255),
                    Textarea::make('value')
                        ->label('Моя ценность')
                        ->required()
                        ->maxLength(255),
                ])
                ->modalHeading('Редактирование ценности'),

            // Действие для удаления
            DeleteAction::make()
                ->label('Удалить')
                ->action(function (Value $record) {
                    // Удаление записи
                    $record->delete();
                    // Можешь добавить сообщения об успешном удалении или другие действия
                })
                ->modalHeading('Удалить ценность')
                ->modalSubheading('Вы уверены, что хотите удалить эту ценность?'),
        ];
    }

    // Настройка фильтров для таблицы
    protected function getTableFilters(): array
    {
        return [
            SelectFilter::make('sphere')
                ->label('Фильтр по сфере') // Лейбл для фильтра
                ->options(fn () => Value::query()
                    ->distinct()
                    ->pluck('sphere', 'sphere')
                    ->toArray()),
        ];
    }

    protected function getTableQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return Value::query()->where('user_id', auth()->id());
    }

}
