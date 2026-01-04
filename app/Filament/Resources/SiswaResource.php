<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SiswaResource\Pages;
use App\Models\Siswa;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
// IMPORT TAMBAHAN BUAT TOMBOL CUSTOM
use Filament\Tables\Actions\Action;
use Illuminate\Support\HtmlString;

class SiswaResource extends Resource
{
    protected static ?string $model = Siswa::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'Data Siswa';
    
    // BIAR RAPI DI SIDEBAR (Masuk Grup Data Sekolah)
    protected static ?string $navigationGroup = 'Data Sekolah';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('nisn')
                    ->label('NISN')
                    ->required()
                    ->maxLength(10)
                    ->numeric(),
                
                TextInput::make('nis')
                    ->label('NIS')
                    ->required()
                    ->maxLength(8),

                TextInput::make('nama')
                    ->label('Nama Lengkap')
                    ->required()
                    ->maxLength(50),

                // Dropdown Kelas
                Select::make('id_kelas')
                    ->label('Kelas')
                    ->relationship('kelas', 'nama_kelas') 
                    ->searchable() 
                    ->preload() 
                    ->required(),

                // Dropdown SPP
                Select::make('id_spp')
                    ->label('Tarif SPP')
                    ->relationship('spp', 'tahun') 
                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->tahun} - Rp " . number_format($record->nominal, 0, ',', '.'))
                    ->searchable()
                    ->preload()
                    ->required(),

                TextInput::make('no_telp')
                    ->label('No. Telepon')
                    ->tel()
                    ->maxLength(15),

                Textarea::make('alamat')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nis')->label('NIS')->searchable(),
                TextColumn::make('nama')->label('Nama')->searchable(),
                TextColumn::make('kelas.nama_kelas')->label('Kelas')->sortable(),
                
                // === KOLOM BARU: STATUS TUNGGAKAN (HITUNG OTOMATIS) ===
                TextColumn::make('status_tunggakan')
                    ->label('Status SPP')
                    ->getStateUsing(function ($record) {
                        // Mengambil data dari Model Siswa (getInfoTunggakanAttribute)
                        $info = $record->info_tunggakan;
                        
                        if ($info['total_bulan'] == 0) {
                            return 'LUNAS';
                        }
                        
                        $rupiah = number_format($info['total_rupiah'], 0, ',', '.');
                        return "Nunggak {$info['total_bulan']} Bulan (Rp $rupiah)";
                    })
                    ->badge()
                    ->color(fn (string $state): string => match (true) {
                        $state === 'LUNAS' => 'success', // Hijau kalau lunas
                        default => 'danger', // Merah kalau nunggak
                    }),

                TextColumn::make('no_telp')->label('Telepon'),
            ])
            ->filters([
                // === FITUR FILTER KELAS ===
                SelectFilter::make('id_kelas')
                    ->label('Filter per Kelas')
                    ->relationship('kelas', 'nama_kelas')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                
                // === TOMBOL MATA (CEK RINCIAN TUNGGAKAN) ===
                Action::make('cek_tunggakan')
                    ->label('Cek Tunggakan')
                    ->icon('heroicon-o-exclamation-circle')
                    ->color('warning')
                    ->modalHeading('Rincian Tunggakan SPP')
                    ->modalSubmitAction(false) // Tombol submit ilangin aja (cuma info)
                    ->modalContent(function ($record) {
                        $info = $record->info_tunggakan;
                        
                        // Kalau lunas, tampilkan pesan simpel
                        if ($info['total_bulan'] == 0) {
                            return new HtmlString("<div class='text-center text-success-600 font-bold'>Alhamdulillah, Siswa ini sudah LUNAS!</div>");
                        }

                        $listBulan = implode(', ', $info['list_bulan']);
                        $totalRp = number_format($info['total_rupiah'], 0, ',', '.');
                        
                        // Render HTML buat isi Modal
                        return new HtmlString("
                            <div class='space-y-4'>
                                <div class='p-4 border border-danger-200 bg-danger-50 rounded-lg'>
                                    <div class='text-sm text-danger-600'>Total Tagihan:</div>
                                    <div class='text-2xl font-bold text-danger-700'>Rp {$totalRp}</div>
                                </div>
                                <div>
                                    <div class='text-sm font-medium text-gray-500 mb-2'>
                                        Belum membayar <b>{$info['total_bulan']} bulan</b>:
                                    </div>
                                    <div class='p-3 bg-gray-100 rounded text-sm text-gray-700 font-mono break-words'>
                                        {$listBulan}
                                    </div>
                                </div>
                            </div>
                        ");
                    }),

                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSiswas::route('/'),
            'create' => Pages\CreateSiswa::route('/create'),
            'edit' => Pages\EditSiswa::route('/{record}/edit'),
        ];
    }
}