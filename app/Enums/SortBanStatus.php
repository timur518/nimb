<?php

namespace App\Enums;
use Mokhosh\FilamentKanban\Concerns\IsKanbanStatus;

enum SortBanStatus: string
{
    use IsKanbanStatus;
    case TODO = 'todo';
    case I_WANT = 'i_want';
    case THEY_WANT = 'they_want';

    public function label(): string
    {
        return match ($this) {
            self::TODO => 'Нужно сделать',
            self::I_WANT => 'Я хочу',
            self::THEY_WANT => 'Я должен',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn ($case) => [
            $case->value => $case->label(),
        ])->toArray();
    }

}

