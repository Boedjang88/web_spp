<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SppResource\Pages;
use App\Models\Spp;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;

class SppResource extends Resource
{
    protected static ?string $model = Spp::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar'; // Icon Dollar
    
    // BIAR RAPI DI SIDEBAR
    protected static ?string $navigationGroup = 'Data Master';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('tahun')
                    ->numeric()
                    ->required()
                    ->label('Tahun Ajaran')
                    ->maxLength(4),
                
                TextInput::make('nominal')
                    ->numeric()
                    ->required()
                    ->label('Nominal SPP')
                    ->prefix('Rp'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tahun')->sortable(),
                TextColumn::make('nominal')->money('IDR')->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
    
    public static function getRelations(): array { return []; }
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSpps::route('/'),
            'create' => Pages\CreateSpp::route('/create'),
            'edit' => Pages\EditSpp::route('/{record}/edit'),
        ];
    }
}