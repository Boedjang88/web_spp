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
    protected static ?string $navigationGroup = 'Akademik';
    protected static ?int $navigationSort = 4;
    protected static ?string $recordTitleAttribute = 'nama_mapel';

    public static function getGloballySearchableAttributes(): array
    {
        return ['kode_mapel', 'nama_mapel', 'kelompok'];
    }

    public static function getGlobalSearchResultTitle(\Illuminate\Database\Eloquent\Model $record): string
    {
        return "{$record->nama_mapel} ({$record->kode_mapel})";
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Kurikulum & Mata Kuliah')
                    ->description('Kode mata kuliah, nama mata kuliah, kelompok/SKS, dan batas kelulusan.')
                    ->icon('heroicon-o-book-open')
                    ->columns(2)
                    ->schema([
                        TextInput::make('kode_mapel')
                            ->label('Kode Mata Kuliah')
                            ->placeholder('Contoh: IF101 atau CS204')
                            ->required()
                            ->maxLength(20),

                        TextInput::make('nama_mapel')
                            ->label('Nama Mata Kuliah')
                            ->placeholder('Contoh: Struktur Data & Algoritma')
                            ->required()
                            ->maxLength(100),

                        TextInput::make('kelompok')
                            ->label('Kelompok / Beban SKS')
                            ->placeholder('Contoh: Wajib Prodi (3 SKS)')
                            ->required()
                            ->maxLength(50),

                        TextInput::make('kkm')
                            ->label('Standar KKM / Batas Nilai')
                            ->numeric()
                            ->default(75)
                            ->suffix('Poin')
                            ->required(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->striped()
            ->columns([
                TextColumn::make('kode_mapel')
                    ->label('Kode Matkul')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('Kode matkul disalin!'),
                TextColumn::make('nama_mapel')
                    ->label('Nama Mata Kuliah')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('kelompok')
                    ->label('Kelompok / SKS')
                    ->badge()
                    ->color('gray')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('kkm')
                    ->label('Batas KKM')
                    ->sortable()
                    ->badge()
                    ->color('warning')
                    ->toggleable(),
                TextColumn::make('jadwals_count')
                    ->counts('jadwals')
                    ->label('Sesi Perkuliahan')
                    ->badge()
                    ->color('info')
                    ->toggleable(),
            ])
            ->emptyStateHeading('Belum Ada Mata Kuliah')
            ->emptyStateDescription('Daftarkan mata kuliah baru untuk memulai penyusunan kurikulum dan jadwal perkuliahan.')
            ->emptyStateIcon('heroicon-o-book-open')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()->label('Tambah Mata Kuliah'),
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
