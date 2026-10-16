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
    protected static ?string $navigationLabel = 'Tarif UKT & Biaya Kuliah';
    protected static ?string $navigationGroup = 'Keuangan';
    protected static ?int $navigationSort = 2;
    protected static ?string $recordTitleAttribute = 'tahun';

    public static function getGloballySearchableAttributes(): array
    {
        return ['tahun', 'nominal'];
    }

    public static function getGlobalSearchResultTitle(\Illuminate\Database\Eloquent\Model $record): string
    {
        return "Tarif UKT {$record->tahun} (Rp " . number_format($record->nominal, 0, ',', '.') . ")";
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Matriks Tarif UKT Per Semester')
                    ->description('Pengaturan besaran tarif biaya pendidikan/UKT berdasarkan tahun angkatan.')
                    ->icon('heroicon-o-currency-dollar')
                    ->columns(2)
                    ->schema([
                        TextInput::make('tahun')
                            ->numeric()
                            ->required()
                            ->label('Tahun Angkatan / Akademik')
                            ->placeholder('Contoh: 2026')
                            ->maxLength(4),
                        
                        TextInput::make('nominal')
                            ->numeric()
                            ->required()
                            ->label('Nominal Tarif UKT / Semester')
                            ->placeholder('Contoh: 3500000')
                            ->prefix('Rp'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->striped()
            ->columns([
                TextColumn::make('tahun')
                    ->label('Tahun Angkatan')
                    ->sortable(),
                TextColumn::make('nominal')
                    ->label('Nominal UKT')
                    ->money('IDR')
                    ->sortable(),
                TextColumn::make('siswas_count')
                    ->counts('siswas')
                    ->label('Mahasiswa Aktif')
                    ->badge()
                    ->color('info')
                    ->toggleable(),
            ])
            ->emptyStateHeading('Belum Ada Tarif UKT')
            ->emptyStateDescription('Tetapkan matriks tarif UKT atau SPP untuk perhitungan tagihan berkala mahasiswa.')
            ->emptyStateIcon('heroicon-o-currency-dollar')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()->label('Tambah Tarif UKT'),
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