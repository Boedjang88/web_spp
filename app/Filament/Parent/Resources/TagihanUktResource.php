<?php

namespace App\Filament\Parent\Resources;

use App\Filament\Parent\Resources\TagihanUktResource\Pages;
use App\Models\TagihanUkt;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class TagihanUktResource extends Resource
{
    protected static ?string $model = TagihanUkt::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    protected static ?string $navigationLabel = 'Informasi Tagihan UKT';
    protected static ?string $modelLabel = 'Tagihan UKT Mahasiswa';
    protected static ?string $pluralModelLabel = 'Daftar Tagihan UKT';
    protected static ?string $navigationGroup = 'Keuangan';
    protected static ?int $navigationSort = 1;

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
            ->with(['tahunAkademik', 'mahasiswa']);
    }

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tahunAkademik.nama_tahun')
                    ->label('Tahun Akademik')
                    ->sortable(),

                TextColumn::make('nomor_invoice')
                    ->label('No. Invoice')
                    ->searchable()
                    ->copyable(),

                TextColumn::make('nomor_va')
                    ->label('Nomor Virtual Account')
                    ->searchable()
                    ->copyable()
                    ->badge()
                    ->color('primary'),

                TextColumn::make('total_harus_bayar')
                    ->label('Harus Dibayar')
                    ->money('IDR')
                    ->sortable(),

                TextColumn::make('total_sudah_bayar')
                    ->label('Sudah Dibayar')
                    ->money('IDR')
                    ->sortable(),

                TextColumn::make('status_pembayaran')
                    ->label('Status')
                    ->badge()
                    ->colors([
                        'success' => 'Lunas',
                        'warning' => 'Sebagian',
                        'danger' => 'Belum Bayar',
                    ]),

                TextColumn::make('tgl_jatuh_tempo')
                    ->label('Jatuh Tempo')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->actions([])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTagihanUkts::route('/'),
        ];
    }
}
