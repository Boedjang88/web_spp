<?php

namespace App\Filament\Resources;

use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use pxlrbt\FilamentExcel\Columns\Column;
use App\Filament\Resources\PembayaranResource\Pages;
use App\Models\Pembayaran;
use App\Models\Siswa;
use App\Models\Spp;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Model;

class PembayaranResource extends Resource
{
    protected static ?string $model = Pembayaran::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationLabel = 'Transaksi Pembayaran UKT';
    protected static ?string $navigationGroup = 'Keuangan';
    protected static ?int $navigationSort = 1;

    public static function getGloballySearchableAttributes(): array
    {
        return ['siswa.nama', 'siswa.nisn', 'bulan_dibayar', 'tahun_dibayar'];
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return "KWT-" . str_pad($record->id, 6, '0', STR_PAD_LEFT) . " - " . ($record->siswa?->nama ?? 'Pembayaran');
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Nominal' => 'Rp ' . number_format($record->jumlah_bayar, 0, ',', '.'),
            'Periode' => "{$record->bulan_dibayar} {$record->tahun_dibayar}",
        ];
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()->role === 'admin' || auth()->user()->role === 'superadmin';
    }
    
    public static function canDeleteAny(): bool
    {
        return auth()->user()->role === 'admin' || auth()->user()->role === 'superadmin';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Subjek & Petugas Verifikasi')
                    ->description('Pilih mahasiswa pembayar dan petugas yang memvalidasi transaksi.')
                    ->icon('heroicon-o-user')
                    ->columns(2)
                    ->schema([
                        Select::make('id_siswa')
                            ->label('Mahasiswa')
                            ->options(Siswa::all()->pluck('nama', 'id'))
                            ->searchable()
                            ->preload()
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if (!$state) return;
                                $siswa = Siswa::find($state);
                                if ($siswa && $siswa->id_spp) {
                                    $spp = Spp::find($siswa->id_spp);
                                    if ($spp) {
                                        $set('id_spp', $siswa->id_spp);
                                        $set('jumlah_bayar', $spp->nominal);
                                    }
                                }
                            }),

                        Select::make('id_petugas')
                            ->label('Petugas Verifikasi')
                            ->relationship('petugas', 'name')
                            ->default(auth()->id())
                            ->searchable()
                            ->preload()
                            ->required(),
                    ]),

                Forms\Components\Section::make('Periode Pembayaran')
                    ->description('Waktu transaksi dan bulan/tahun akademik yang dibayarkan.')
                    ->icon('heroicon-o-calendar')
                    ->columns(3)
                    ->schema([
                        DatePicker::make('tgl_bayar')
                            ->label('Tanggal Pembayaran')
                            ->required()
                            ->default(now()),

                        Select::make('bulan_dibayar')
                            ->label('Bulan Dibayar')
                            ->options([
                                'Januari' => 'Januari', 'Februari' => 'Februari', 'Maret' => 'Maret',
                                'April' => 'April', 'Mei' => 'Mei', 'Juni' => 'Juni',
                                'Juli' => 'Juli', 'Agustus' => 'Agustus', 'September' => 'September',
                                'Oktober' => 'Oktober', 'November' => 'November', 'Desember' => 'Desember'
                            ])
                            ->required(),

                        TextInput::make('tahun_dibayar')
                            ->label('Tahun Dibayar')
                            ->numeric()
                            ->required()
                            ->default(date('Y'))
                            ->maxLength(4),
                    ]),

                Forms\Components\Section::make('Nominal & Tarif UKT')
                    ->description('Penetapan tarif UKT otomatis berdasarkan data siswa.')
                    ->icon('heroicon-o-currency-dollar')
                    ->columns(2)
                    ->schema([
                        Select::make('id_spp')
                            ->label('Tarif UKT')
                            ->options(Spp::all()->pluck('tahun', 'id'))
                            ->disabled()
                            ->dehydrated()
                            ->required(),

                        TextInput::make('jumlah_bayar')
                            ->label('Jumlah Bayar')
                            ->prefix('Rp')
                            ->readOnly()
                            ->required(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->striped()
            ->headerActions([
                ExportAction::make()
                    ->label('Export Excel UKT')
                    ->color('success')
                    ->icon('heroicon-o-document-arrow-down')
                    ->exports([
                        ExcelExport::make()
                            ->fromTable()
                            ->withFilename('Laporan_Pembayaran_UKT_' . date('Y-m-d'))
                            ->withColumns([
                                Column::make('tgl_bayar')->heading('Tanggal'),
                                Column::make('siswa.nama')->heading('Nama Mahasiswa'),
                                Column::make('siswa.kelas.nama_kelas')->heading('Kelas Kuliah'),
                                Column::make('bulan_dibayar')->heading('Bulan'),
                                Column::make('tahun_dibayar')->heading('Tahun'),
                                Column::make('jumlah_bayar')->heading('Nominal UKT'),
                                Column::make('petugas.name')->heading('Petugas'),
                            ]),
                    ]),
            ])
            ->columns([
                TextColumn::make('petugas.name')
                    ->label('Petugas')
                    ->toggleable(),
                TextColumn::make('siswa.nama')
                    ->label('Mahasiswa')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('bulan_dibayar')
                    ->label('Bulan')
                    ->sortable(),
                TextColumn::make('tahun_dibayar')
                    ->label('Tahun')
                    ->sortable(),
                TextColumn::make('jumlah_bayar')
                    ->money('IDR')
                    ->label('Nominal UKT')
                    ->sortable(),
                TextColumn::make('tgl_bayar')
                    ->date('d M Y')
                    ->label('Tgl Bayar')
                    ->sortable()
                    ->toggleable(),
            ])
            ->emptyStateHeading('Belum Ada Transaksi Pembayaran UKT')
            ->emptyStateDescription('Catat transaksi pembayaran UKT pertama untuk menerbitkan kwitansi resmi.')
            ->emptyStateIcon('heroicon-o-banknotes')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()->label('Catat Pembayaran Baru'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                
                Action::make('cetak')
                    ->label('Cetak Kwitansi')
                    ->icon('heroicon-o-printer')
                    ->color('info')
                    ->url(fn (Pembayaran $record) => route('cetak.kwitansi', $record->id))
                    ->openUrlInNewTab(),

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
            'index' => Pages\ListPembayarans::route('/'),
            'create' => Pages\CreatePembayaran::route('/create'),
            'edit' => Pages\EditPembayaran::route('/{record}/edit'),
        ];
    }
}