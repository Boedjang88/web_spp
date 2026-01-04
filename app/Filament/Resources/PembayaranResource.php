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
    protected static ?string $navigationLabel = 'Transaksi Pembayaran';
    
    // BIAR RAPI DI SIDEBAR (Grup Sendiri biar mencolok)
    protected static ?string $navigationGroup = 'Keuangan';

    // ... code sebelumnya

    // Cuma user dengan role 'admin' yang boleh hapus pembayaran
    public static function canDelete(Model $record): bool
    {
        return auth()->user()->role === 'admin';
    }
    
    // Cuma admin yang bisa hapus massal
    public static function canDeleteAny(): bool
    {
        return auth()->user()->role === 'admin';
    }

    // ... sisa code form dan table


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('id_petugas')
                    ->label('Petugas Penerima')
                    ->relationship('petugas', 'name')
                    ->default(auth()->id())
                    ->searchable()
                    ->preload()
                    ->required(),

                Select::make('id_siswa')
                    ->label('Siswa')
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

                DatePicker::make('tgl_bayar')->required()->default(now()),

                Select::make('bulan_dibayar')
                    ->options([
                        'Januari' => 'Januari', 'Februari' => 'Februari', 'Maret' => 'Maret',
                        'April' => 'April', 'Mei' => 'Mei', 'Juni' => 'Juni',
                        'Juli' => 'Juli', 'Agustus' => 'Agustus', 'September' => 'September',
                        'Oktober' => 'Oktober', 'November' => 'November', 'Desember' => 'Desember'
                    ])->required(),

                TextInput::make('tahun_dibayar')->numeric()->required()->default(date('Y'))->maxLength(4),

                Select::make('id_spp')
                    ->label('Tarif SPP')
                    ->options(Spp::all()->pluck('tahun', 'id'))
                    ->disabled()
                    ->dehydrated()
                    ->required(),

                TextInput::make('jumlah_bayar')->prefix('Rp')->readOnly()->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->headerActions([
                // === TOMBOL EXPORT EXCEL (FITUR BARU) ===
                ExportAction::make()
                    ->label('Export Excel')
                    ->color('success') // Warna Hijau
                    ->icon('heroicon-o-document-arrow-down')
                    ->exports([
                        ExcelExport::make()
                            ->fromTable() // Ambil data sesuai filter tabel saat ini
                            ->withFilename('Laporan_Pembayaran_' . date('Y-m-d'))
                            ->withColumns([
                                Column::make('tgl_bayar')->heading('Tanggal'),
                                Column::make('siswa.nama')->heading('Nama Siswa'),
                                Column::make('siswa.kelas.nama_kelas')->heading('Kelas'),
                                Column::make('bulan_dibayar')->heading('Bulan'),
                                Column::make('tahun_dibayar')->heading('Tahun'),
                                Column::make('jumlah_bayar')->heading('Nominal'),
                                Column::make('petugas.name')->heading('Petugas'),
                            ]),
                    ]),
            ])
            ->columns([
                TextColumn::make('petugas.name')->label('Petugas'),
                TextColumn::make('siswa.nama')->label('Siswa')->searchable()->sortable(),
                TextColumn::make('bulan_dibayar')->label('Bulan'),
                TextColumn::make('tahun_dibayar')->label('Tahun'),
                TextColumn::make('jumlah_bayar')->money('IDR'),
                TextColumn::make('tgl_bayar')->date('d M Y'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                
                // Tombol Cetak Kwitansi (yg sebelumnya udah kita buat)
                \Filament\Tables\Actions\Action::make('cetak')
                    ->label('Cetak')
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