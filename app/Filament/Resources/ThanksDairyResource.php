<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ThanksDairyResource\Pages;
use App\Models\ThanksDairy;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Filters\Filter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\{Grid, Section, Textarea, DatePicker, Hidden, Group};

class ThanksDairyResource extends Resource
{
    protected static ?string $model = ThanksDairy::class;
    protected static ?string $modelLabel = 'Дневник благодарности';
    protected static ?string $pluralModelLabel = 'Дневник благодарности';
    protected static ?string $navigationIcon = 'heroicon-o-sparkles';
    protected static ?string $navigationGroup = 'Личное';
    protected static ?string $label = 'Дневник благодарности';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Hidden::make('user_id')
                    ->default(fn () => Auth()->id()),

                Section::make('Запись в дневнике благодарности')
                    ->description('Настройся. Наполни день благодарностью, принятием и намерением.')
                    ->schema([
                        Grid::make()
                            ->schema([
                                DatePicker::make('date')
                                    ->label('📅 Дата')
                                    ->default(now())
                                    ->required(),
                            ]),

                        Group::make()
                            ->schema([
                                Textarea::make('thanks')
                                    ->label('🙏 Благодарю')
                                    ->placeholder('За что ты благодарен сегодня?')
                                    ->autosize()
                                    ->rows(4)
                                    ->required(),

                                Textarea::make('acceptance')
                                    ->label('💫 Принимаю')
                                    ->placeholder('Что ты принимаешь в свою реальность как будто это уже случилось?')
                                    ->autosize()
                                    ->rows(4),

                                Textarea::make('creation')
                                    ->label('🔥 Создаю')
                                    ->placeholder('Что ты выбираешь создавать, направлять своё внимание?')
                                    ->autosize()
                                    ->rows(4),

                                Textarea::make('note')
                                    ->label('📝 Заметка')
                                    ->placeholder('Мантра, ощущение, озарение — всё, что хочется зафиксировать.')
                                    ->autosize()
                                    ->rows(3),
                            ])
                            ->columns(1)
                    ])
                    ->columns(1)
                    ->columnSpanFull(),
            ]);
    }


    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->label('Дата')
                    ->sortable(),
                Tables\Columns\TextColumn::make('thanks')
                    ->label('Благодарность')
                    ->limit(50),
            ])
            ->defaultSort('date', 'desc')
            ->emptyStateHeading('Дневник благодарности пуст')
            ->emptyStateDescription('Запишите первую благодарность в Нимб')
            ->emptyStateActions([Tables\Actions\CreateAction::make()->label('Добавить первую запись'),])
            ->actions([
                EditAction::make()
                    ->label('Изменить')
                    ->icon('heroicon-o-pencil'),
                DeleteAction::make()
                    ->label('')
                    ->icon('heroicon-o-trash'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Удалить выбранные'),
                ]),
            ]);
    }


    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListThanksDairies::route('/'),
            'create' => Pages\CreateThanksDairy::route('/create'),
            'edit' => Pages\EditThanksDairy::route('/{record}/edit'),
            'view' => Pages\ViewThanksDairy::route('/{record}'),
            ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('user_id', auth()->id());
    }
}
