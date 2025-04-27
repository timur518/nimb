<?php

namespace App\Filament\Resources\LifeRuleResource\Widgets;

use App\Models\LifeRule;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Hidden;
use Filament\Tables;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Filters\SelectFilter;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class LifeRuleTableWidget extends BaseWidget
{
    protected int|string|array $columnSpan = 'full';

    protected function getTableHeading(): string
    {
        return 'Мои правила жизни';
    }

    protected function getTableDescription(): string
    {
        return 'Создайте набор принципов, которые будут помогать вам принимать правильные решения. Например: Не борюсь за признание, иду своим путём.';
    }

    public function getTableEmptyStateHeading(): string
    {
        return 'Пока не записано ни одного жизненного правила';
    }

    protected function getTableQuery(): Builder
    {
        return LifeRule::query()->where('user_id', auth()->id());
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('sphere')
                ->label('Сфера')
                ->sortable()
                ->searchable(),
            Tables\Columns\TextColumn::make('rule')
                ->label('Правила и принципы')
                ->limit(100)
                ->tooltip(fn(LifeRule $record) => $record->rule),
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
                    Textarea::make('rule')
                        ->label('Правило или принцип')
                        ->cols(5)
                        ->required(),
                ])
                ->modalHeading('Редактирование правила'),

            // Действие для удаления
            DeleteAction::make()
                ->label('Удалить')
                ->action(function (LifeRule $record) {
                    // Удаление записи
                    $record->delete();
                    // Можно добавить уведомление об успешном удалении
                })
                ->modalHeading('Удалить правило')
                ->modalSubheading('Вы уверены, что хотите удалить это правило?'),
        ];
    }

    protected function getTableHeaderActions(): array
    {
        return [
            CreateAction::make('create')
                ->label('Добавить правило')
                ->form([
                    Hidden::make('user_id')
                        ->default(fn () => Auth()->id()),
                    TextInput::make('sphere')
                        ->label('Сфера')
                        ->required()
                        ->maxLength(255),
                    Textarea::make('rule')
                        ->label('Правило или принцип')
                        ->cols(5)
                        ->required(),
                ])
                ->modalHeading('Добавить новое правило'),
        ];
    }

    protected function getTableFilters(): array
    {
        return [
            SelectFilter::make('sphere')
                ->label('Фильтр по сфере')
                ->options(fn() => LifeRule::query()->distinct()->pluck('sphere', 'sphere')->toArray()),
        ];
    }
}
