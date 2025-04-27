<?php

namespace App\Filament\Resources\FutureVisionResource\Widgets;

use App\Models\FutureVision;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Hidden;
use Filament\Tables;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class FutureVisionTableWidget extends BaseWidget
{
    protected int|string|array $columnSpan = 'full';

    protected function getTableHeading(): string
    {
        return 'Мое видение будущего';
    }

    protected function getTableDescription(): string
    {
        return 'Запишите, как вы видите свое будущее, чтобы направлять свои действия в этом направлении. Как я хочу жить через 5–10 лет?
        (описание атмосферы, состояния, окружения). Можно записать видение по каждой сфере жизни отдельно.';
    }

    public function getTableEmptyStateHeading(): string
    {
        return 'Пока не записано ни одного видения';
    }

    protected function getTableQuery(): Builder
    {
        return FutureVision::query()->where('user_id', auth()->id());
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('title')
                ->label('Сфера')
                ->sortable()
                ->searchable(),
            Tables\Columns\TextColumn::make('vision_text')
                ->label('Мое видение')
                ->limit(300)
                ->tooltip(fn(FutureVision $record) => $record->vision_text),
        ];
    }

    // Добавляем действия редактирования и удаления
    protected function getTableActions(): array
    {
        return [
            // Действие для редактирования
            EditAction::make()
                ->label('Редактировать')
                ->form([
                    TextInput::make('title')
                        ->label('Сфера')
                        ->required()
                        ->maxLength(255),
                    Textarea::make('vision_text')
                        ->label('Мое видение')
                        ->required()
                        ->rows(12)
                        ->autosize(),
                ])
                ->modalHeading('Редактирование видения'),

            // Действие для удаления
            DeleteAction::make()
                ->label('Удалить')
                ->action(function (FutureVision $record) {
                    // Удаление записи
                    $record->delete();
                    // Можно добавить уведомление об успешном удалении
                })
                ->modalHeading('Удалить видение')
                ->modalSubheading('Вы уверены, что хотите удалить это видение?'),
        ];
    }

    protected function getTableHeaderActions(): array
    {
        return [
            CreateAction::make('create')
                ->label('Добавить запись')
                ->form([
                    Hidden::make('user_id')
                        ->default(fn () => Auth()->id()),
                    TextInput::make('title')
                        ->label('Сфера')
                        ->required()
                        ->maxLength(255),
                    Textarea::make('vision_text')
                        ->label('Мое видение')
                        ->required()
                        ->rows(12)
                        ->autosize(),
                ])
                ->modalHeading('Добавление нового видения'),
        ];
    }

    protected function getTableFilters(): array
    {
        return [
            Tables\Filters\Filter::make('title')
                ->form([
                    TextInput::make('title')
                        ->label('Поиск по заголовку'),
                ])
                ->query(function (Builder $query, array $data) {
                    return $query
                        ->when($data['title'], fn ($q, $value) => $q->where('title', 'like', "%{$value}%"));
                }),
        ];
    }
}
