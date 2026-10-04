<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MataKuliahResource\Pages;
use App\Models\Mapel;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;

class MataKuliahResource extends Resource
{
    protected static ?string $model = Mapel::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';
    protected static ?string $navigationLabel = 'Data Mata Kuliah';
    protected static ?string $navigationGroup = 'Akademik & Master';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('kode_mapel')
                    ->label('Kode Matkul')
                    ->required()
                    ->maxLength(20),

                TextInput::make('nama_mapel')
                    ->label('Nama Mata Kuliah')
                    ->required()
                    ->maxLength(100),

                TextInput::make('kelompok')
                    ->label('Kelompok / SKS')
                    ->required()
                    ->maxLength(50),

                TextInput::make('kkm')
                    ->label('Standar KKM / Batas Nilai')
                    ->numeric()
                    ->default(75)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('kode_mapel')->label('Kode Matkul')->searchable()->sortable(),
                TextColumn::make('nama_mapel')->label('Nama Mata Kuliah')->searchable()->sortable(),
                TextColumn::make('kelompok')->label('Kelompok / SKS')->searchable(),
                TextColumn::make('kkm')->label('Batas KKM')->sortable(),
                TextColumn::make('jadwals_count')
                    ->counts('jadwals')
                    ->label('Sesi Perkuliahan')
                    ->badge()
                    ->color('info'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMataKuliahs::route('/'),
            'create' => Pages\CreateMataKuliah::route('/create'),
            'edit' => Pages\EditMataKuliah::route('/{record}/edit'),
        ];
    }
}
