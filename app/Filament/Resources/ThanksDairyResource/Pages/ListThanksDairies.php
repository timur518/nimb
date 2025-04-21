<?php

namespace App\Filament\Resources\ThanksDairyResource\Pages;

use App\Filament\Resources\ThanksDairyResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListThanksDairies extends ListRecords
{
    protected static string $resource = ThanksDairyResource::class;
    protected static ?string $title = 'Дневник благодарности';
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
