<?php

namespace App\Filament\Resources\ThanksDairyResource\Pages;

use App\Models\ThanksDairy;
use App\Filament\Resources\ThanksDairyResource;
use Filament\Actions;
use Illuminate\Database\Eloquent\Model;
use Filament\Resources\Pages\ViewRecord;


class ViewThanksDairy extends ViewRecord
{
    protected static string $resource = ThanksDairyResource::class;
    protected static ?string $model = ThanksDairy::class;
    protected static bool $canCreateAnother = false;
    protected static ?string $title = 'Запись из дневника';

    // Указываем кастомный шаблон
    public function getView(): string
    {
        return 'filament.resources.thanks-dairy-resource.pages.view-thanks-dairy'; // Путь к твоему Blade-шаблону
    }
}
