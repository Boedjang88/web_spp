<?php

namespace App\Filament\Parent\Resources;

use App\Filament\Parent\Resources\NilaiObeResource\Pages;
use App\Models\NilaiObe;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class NilaiObeResource extends Resource
{
    protected static ?string $model = NilaiObe::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    protected static ?string $navigationLabel = 'Nilai Capaian OBE';
    protected static ?string $modelLabel = 'Capaian Pembelajaran OBE';
    protected static ?string $pluralModelLabel = 'Daftar Nilai Capaian OBE';
    protected static ?string $navigationGroup = 'Akademik';
    protected static ?int $navigationSort = 2;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }

    public static function getEloquentQuery(): Builder
    {
        $user = auth()->user();
        $studentId = $user?->id_mahasiswa ?? $user?->id_siswa ?? 0;

        return parent::getEloquentQuery()
            ->whereHas('krsDetail.krs', function ($q) use ($studentId) {
                $q->where('id_siswa', $studentId);
            })
            ->with(['krsDetail.kelasKuliah.mataKuliah', 'cpmk']);
    }

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('krsDetail.kelasKuliah.mataKuliah.nama_mk')
                    ->label('Mata Kuliah')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('cpmk.kode_cpmk')
                    ->label('Kode CPMK')
                    ->badge()
                    ->color('primary')
                    ->sortable(),

                TextColumn::make('cpmk.deskripsi_cpmk_id')
                    ->label('Deskripsi Kompetensi CPMK')
                    ->limit(50),

                TextColumn::make('skor_pencapaian')
                    ->label('Skor Capaian')
                    ->numeric(decimalPlaces: 2)
                    ->sortable(),

                IconColumn::make('is_terpenuhi')
                    ->label('Ketercapaian')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
            ])
            ->actions([])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNilaiObes::route('/'),
        ];
    }
}
