<?php

namespace App\Filament\Resources\KanbanCategoriesResource\Pages;

use App\Filament\Resources\KanbanCategoriesResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateKanbanCategories extends CreateRecord
{
    protected static ?string $title = 'Создать категорию';
    protected static string $resource = KanbanCategoriesResource::class;
    protected static bool $canCreateAnother = false;

    //customize redirect after create
    public function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
