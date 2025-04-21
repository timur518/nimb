<?php

namespace App\Filament\Resources\ThanksDairyResource\Pages;

use App\Filament\Resources\ThanksDairyResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditThanksDairy extends EditRecord
{
    protected static string $resource = ThanksDairyResource::class;

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
