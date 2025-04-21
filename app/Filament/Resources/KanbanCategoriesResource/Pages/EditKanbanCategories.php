<?php

namespace App\Filament\Resources\KanbanCategoriesResource\Pages;

use App\Filament\Resources\KanbanCategoriesResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditKanbanCategories extends EditRecord
{
    protected static ?string $title = 'Изменить категорию';
    protected static string $resource = KanbanCategoriesResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }

    //customize redirect after create
    public function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
