<?php

namespace App\Enums;
use Mokhosh\FilamentKanban\Concerns\IsKanbanStatus;

enum GoalsBanStatus: string
{
    use IsKanbanStatus;
    case TODO = 'todo';
    case INPROCESS = 'inprocess';
    case DONE = 'done';

    public function label(): string
    {
        return match ($this) {
            self::TODO => 'Мечта записана',
            self::INPROCESS => 'В процессе достижения',
            self::DONE => 'Успешно достигнуты',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn ($case) => [
            $case->value => $case->label(),
        ])->toArray();
    }

}

