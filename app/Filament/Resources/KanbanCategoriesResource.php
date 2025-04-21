<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KanbanCategoriesResource\Pages;
use App\Filament\Resources\KanbanCategoriesResource\RelationManagers;
use App\Models\KanbanCategories;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Hidden;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class KanbanCategoriesResource extends Resource
{
    protected static ?string $model = KanbanCategories::class;
    protected static ?string $navigationGroup = 'Настройки';
    protected static ?string $navigationLabel = 'Категории дел';
    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?string $title = 'Категории дел в канбане';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Hidden::make('user_id')
                    ->default(Auth()->id()),
                TextInput::make('name')
                    ->label('Название категории')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                ->label('Название категории')
            ])
            ->filters([
                //
            ])
            ->emptyStateHeading('Категории пока не добавлены')
            ->emptyStateDescription('Создайте первую категорию, чтобы начать работать с канбаном.')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()->label('Добавить первую категорию'),
            ])
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
            'index' => Pages\ListKanbanCategories::route('/'),
            'create' => Pages\CreateKanbanCategories::route('/create'),
            'edit' => Pages\EditKanbanCategories::route('/{record}/edit'),
        ];
    }
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('user_id', auth()->id());
    }
}
