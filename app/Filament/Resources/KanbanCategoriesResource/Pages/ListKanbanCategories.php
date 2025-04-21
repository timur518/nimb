<?php

namespace App\Filament\Resources\KanbanCategoriesResource\Pages;

use App\Filament\Resources\KanbanCategoriesResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListKanbanCategories extends ListRecords
{
    protected static ?string $title = 'Категории дел в канбане';
    protected static string $resource = KanbanCategoriesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Добавить категорию')
                ->icon('heroicon-o-plus-circle'),
        ];
    }
}
