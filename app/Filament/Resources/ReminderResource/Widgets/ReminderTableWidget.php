<?php

namespace App\Filament\Resources\ReminderResource\Widgets;

use App\Models\Reminder;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Textarea;
use Filament\Tables;
use Filament\Tables\Actions\CreateAction;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class ReminderTableWidget extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    protected function getTableHeading(): string
    {
        return 'Мои напоминания себе';
    }

    protected function getTableDescription(): string
    {
        return 'Короткие фразы или цитаты, которые вдохновляют и напоминают о вашем пути. Например: Не суетись — доверься потоку';
    }

    public function getTableEmptyStateHeading(): string
    { return 'Пока не записано ни одного напоминания себе'; }

    protected function getTableQuery(): Builder
    {
        return Reminder::query()->where('user_id', auth()->id());
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('note')
                ->label('Напоминание себе')
                ->limit(250)
                ->tooltip(fn (Reminder $record) => $record->note),
        ];
    }

    protected function getTableHeaderActions(): array
    {
        return [
            CreateAction::make('create')
                ->label('Добавить записку')
                ->form([
                    Hidden::make('user_id')
                    ->default(auth()->id()),
                    Textarea::make('note')
                        ->required()
                        ->label('Записка себе')
                        ->cols(8)
                        ->maxLength(1000),
                ]),
        ];
    }
}
