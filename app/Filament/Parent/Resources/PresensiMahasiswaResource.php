<?php

namespace App\Filament\Parent\Resources;

use App\Filament\Parent\Resources\PresensiMahasiswaResource\Pages;
use App\Models\PresensiMahasiswa;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class PresensiMahasiswaResource extends Resource
{
    protected static ?string $model = PresensiMahasiswa::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationLabel = 'Presensi Perkuliahan';
    protected static ?string $modelLabel = 'Presensi Perkuliahan Mahasiswa';
    protected static ?string $pluralModelLabel = 'Daftar Presensi Perkuliahan';
    protected static ?string $navigationGroup = 'Akademik';
    protected static ?int $navigationSort = 3;

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
            ->where('id_mahasiswa', $studentId)
            ->with(['bap.kelasKuliah.mataKuliah', 'kelasKuliah.mataKuliah']);
    }

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('kelasKuliah.mataKuliah.nama_mk')
                    ->label('Mata Kuliah')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('bap.pertemuan_ke')
                    ->label('Pertemuan Ke')
                    ->sortable()
                    ->badge(),

                TextColumn::make('status')
                    ->label('Status Kehadiran')
                    ->badge()
                    ->colors([
                        'success' => 'Hadir',
                        'info' => 'Izin',
                        'warning' => 'Sakit',
                        'danger' => 'Alpa',
                    ])
                    ->sortable(),

                TextColumn::make('waktu_hadir')
                    ->label('Waktu Check-In')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
            ])
            ->actions([])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPresensiMahasiswas::route('/'),
        ];
    }
}
