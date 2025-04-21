<?php

namespace App\Enums;
use Mokhosh\FilamentKanban\Concerns\IsKanbanStatus;

enum KanBanStatus: string
{
    use IsKanbanStatus;
    case TODO = 'todo';
    case PLANNED = 'planned';
    case PROCESSI = 'processi';
    case PROCESSO = 'processo';
    case TODAY = 'today';
    case DONE = 'done';

    public function label(): string
    {
        return match ($this) {
            self::TODO => 'Нужно сделать',
            self::PLANNED => 'Запланировано',
            self::PROCESSI => 'В процессе (я)',
            self::PROCESSO => 'В процессе (другие)',
            self::TODAY => 'Сегодня',
            self::DONE => 'Успешно завершено',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn ($case) => [
            $case->value => $case->label(),
        ])->toArray();
    }

}

