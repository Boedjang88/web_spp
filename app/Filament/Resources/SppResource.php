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

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $navigationLabel = 'Tarif UKT (Uang Kuliah Tunggal)';
    protected static ?string $navigationGroup = 'Keuangan & UKT';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('tahun')
                    ->numeric()
                    ->required()
                    ->label('Tahun Akademik')
                    ->maxLength(4),
                
                TextInput::make('nominal')
                    ->numeric()
                    ->required()
                    ->label('Nominal UKT / Semester')
                    ->prefix('Rp'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tahun')->label('Tahun Akademik')->sortable(),
                TextColumn::make('nominal')->label('Nominal UKT')->money('IDR')->sortable(),
                TextColumn::make('siswas_count')
                    ->counts('siswas')
                    ->label('Mahasiswa Aktif')
                    ->badge()
                    ->color('info'),
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