<?php

namespace App\Filament\Resources\ThanksDairyResource\Pages;

use App\Filament\Resources\ThanksDairyResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateThanksDairy extends CreateRecord
{
    protected static string $resource = ThanksDairyResource::class;
    protected static bool $canCreateAnother = false;
    protected static ?string $title = 'Запись в дневник';

    //customize redirect after create
    public function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
