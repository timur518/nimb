<?php

namespace App\Filament\Pages;

use App\Settings\KaidoSetting;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;

class ManageSetting extends SettingsPage
{
    use HasPageShield;
    protected static ?string $navigationGroup = 'Настройки';
    protected static ?string $navigationLabel = 'Настройки системы';
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?int $navigationSort = 10;
    protected static string $settings = KaidoSetting::class;


    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Общая информация')->columns(1)->schema([
                    TextInput::make('site_name')
                        ->label('Название системы')
                        ->required(),
                    Toggle::make('site_active')
                        ->label('Статус сайта'),
                    Toggle::make('registration_enabled')
                        ->label('Разрешить регистрацию'),
                    Toggle::make('password_reset_enabled')
                        ->label('Разрешить восстановление пароля'),
                    Toggle::make('sso_enabled')
                        ->label('Социальная авторизация'),
                ]),
            ]);
    }
}
