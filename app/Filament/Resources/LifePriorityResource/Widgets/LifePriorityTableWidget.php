<?php

namespace App\Filament\Resources\LifePriorityResource\Widgets;

use App\Models\Goals;
use App\Models\LifePriority;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Tables;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Filters\SelectFilter;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class LifePriorityTableWidget extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    protected function getTableHeading(): string
    {
        return 'Жизненные приоритеты';
    }

    protected function getTableDescription(): string
    {
        return 'Список сфер, на которые вы делаете особую ставку. Держите их всегда в фокусе';
    }

    public function getTableEmptyStateHeading(): string
    {
        return 'Пока не записано ни одного приоритета';
    }

    protected function getTableQuery(): Builder
    {
        return LifePriority::query()->where('user_id', auth()->id());
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('sphere')
                ->label('Сфера')
                ->sortable()
                ->searchable(),
            Tables\Columns\TextColumn::make('why_important')
                ->label('Почему важно')
                ->limit(100)
                ->tooltip(fn (LifePriority $record) => $record->why_important),
            Tables\Columns\TextColumn::make('goal.name')
                ->label('Цель на год')
                ->url(fn (LifePriority $record) => '/goals/' . $record->goal_id)
                ->openUrlInNewTab(),
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
                    Textarea::make('why_important')
                        ->label('Почему важно')
                        ->cols(5)
                        ->required(),
                    Select::make('goal_id')
                        ->label('Мини-цель на ближайший год')
                        ->options(fn () => Goals::where('user_id', auth()->id())
                            ->pluck('name', 'id')
                            ->toArray())
                        ->searchable()
                        ->preload()
                        ->nullable(),
                ])
                ->modalHeading('Редактирование приоритета'),

            // Действие для удаления
            DeleteAction::make()
                ->label('Удалить')
                ->action(function (LifePriority $record) {
                    // Удаление записи
                    $record->delete();
                    // Можно добавить уведомление об успешном удалении
                })
                ->modalHeading('Удалить приоритет')
                ->modalSubheading('Вы уверены, что хотите удалить этот приоритет?'),
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
                    Textarea::make('why_important')
                        ->label('Почему важно')
                        ->cols(5)
                        ->required(),
                    Select::make('goal_id')
                        ->label('Мини-цель на ближайший год')
                        ->options(fn () => Goals::where('user_id', auth()->id())
                            ->pluck('name', 'id')
                            ->toArray())
                        ->searchable()
                        ->preload()
                        ->nullable(),
                ])
                ->modalHeading('Добавить приоритет'),
        ];
    }

    protected function getTableFilters(): array
    {
        return [
            SelectFilter::make('sphere')
                ->label('Фильтр по сфере')
                ->options(fn () => LifePriority::query()->distinct()->pluck('sphere', 'sphere')->toArray()),
        ];
    }
}
