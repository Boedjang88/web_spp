<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SiswaResource\Pages;
use App\Models\Siswa;
use App\Services\Academic\EarlyWarningService;
use App\Services\Academic\GraduationClearanceService;
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
use Filament\Tables\Actions\Action;
use Illuminate\Support\HtmlString;

class SiswaResource extends Resource
{
    protected static ?string $model = Siswa::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'Data Mahasiswa';
    protected static ?string $navigationGroup = 'Civitas Akademika';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('nisn')
                    ->label('NIM / NISN')
                    ->required()
                    ->maxLength(20),
                
                TextInput::make('nis')
                    ->label('NPM / Kode Mahasiswa')
                    ->required()
                    ->maxLength(20),

                TextInput::make('nama')
                    ->label('Nama Lengkap Mahasiswa')
                    ->required()
                    ->maxLength(100),

                Select::make('id_kelas')
                    ->label('Kelas Kuliah / Program Studi')
                    ->relationship('kelas', 'nama_kelas') 
                    ->searchable() 
                    ->preload() 
                    ->required(),

                Select::make('id_spp')
                    ->label('Tarif UKT')
                    ->relationship('spp', 'tahun') 
                    ->getOptionLabelFromRecordUsing(fn ($record) => "Tahun {$record->tahun} - Rp " . number_format($record->nominal, 0, ',', '.'))
                    ->searchable()
                    ->preload()
                    ->required(),

                TextInput::make('no_telp')
                    ->label('No. Telepon / WhatsApp')
                    ->tel()
                    ->maxLength(20),

                Textarea::make('alamat')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nisn')->label('NIM / NISN')->searchable()->sortable(),
                TextColumn::make('nama')->label('Nama Mahasiswa')->searchable()->sortable(),
                TextColumn::make('kelas.nama_kelas')->label('Kelas Kuliah')->sortable(),
                
                TextColumn::make('status_tunggakan')
                    ->label('Status UKT')
                    ->getStateUsing(function ($record) {
                        $info = $record->info_tunggakan;
                        if ($info['total_bulan'] == 0) {
                            return 'LUNAS';
                        }
                        $rupiah = number_format($info['total_rupiah'], 0, ',', '.');
                        return "Tunggakan Rp $rupiah";
                    })
                    ->badge()
                    ->color(fn (string $state): string => match (true) {
                        $state === 'LUNAS' => 'success',
                        default => 'danger',
                    }),

                TextColumn::make('no_telp')->label('Telepon'),
            ])
            ->filters([
                SelectFilter::make('id_kelas')
                    ->label('Filter per Kelas Kuliah')
                    ->relationship('kelas', 'nama_kelas')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                
                // === ACTION: AUDIT KELULUSAN (KRS MALPRAKTIK GUARD) ===
                Action::make('audit_kelulusan')
                    ->label('Audit Kelulusan')
                    ->icon('heroicon-o-academic-cap')
                    ->color('success')
                    ->modalHeading('Audit Dynamic Graduation Clearance')
                    ->modalSubmitAction(false)
                    ->modalContent(function ($record) {
                        $service = app(GraduationClearanceService::class);
                        $audit = $service->auditGraduation($record);

                        $isCleared = $audit['is_cleared'];
                        $sksAcquired = $audit['total_sks_acquired'];
                        $sksTarget = $audit['target_sks'];
                        $skpiPoints = $audit['total_skpi_points'];
                        $statusBadge = $isCleared 
                            ? "<span class='px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 font-bold text-xs'>ELIGIBLE / CLEARED FOR GRADUATION</span>"
                            : "<span class='px-3 py-1 rounded-full bg-rose-100 text-rose-800 font-bold text-xs'>BLOCKED BY CLEARANCE GUARD</span>";

                        $failedList = '';
                        foreach ($audit['failed_mandatory_courses'] as $f) {
                            $failedList .= "<li class='text-xs text-rose-700 font-semibold'>- {$f['kode_mk']} {$f['nama_mk']} (Nilai: {$f['grade']})</li>";
                        }
                        if (empty($failedList)) {
                            $failedList = "<li class='text-xs text-emerald-700'>Tidak ada mata kuliah wajib yang tidak lulus.</li>";
                        }

                        return new HtmlString("
                            <div class='space-y-4 text-xs'>
                                <div class='p-3 bg-gray-50 border rounded-lg flex items-center justify-between'>
                                    <div><strong>Status Yudisium:</strong> {$statusBadge}</div>
                                </div>
                                <div class='grid grid-cols-2 gap-3'>
                                    <div class='p-3 bg-blue-50 border border-blue-200 rounded-lg'>
                                        <div class='text-gray-500'>SKS Lulus:</div>
                                        <div class='text-lg font-bold text-blue-700'>{$sksAcquired} / {$sksTarget} SKS</div>
                                    </div>
                                    <div class='p-3 bg-indigo-50 border border-indigo-200 rounded-lg'>
                                        <div class='text-gray-500'>Poin SKPI:</div>
                                        <div class='text-lg font-bold text-indigo-700'>{$skpiPoints} Poin</div>
                                    </div>
                                </div>
                                <div class='p-3 bg-rose-50 border border-rose-200 rounded-lg'>
                                    <div class='font-bold text-rose-800 mb-1'>Evaluasi Matkul Wajib (Bebas Nilai D/E):</div>
                                    <ul class='space-y-1'>{$failedList}</ul>
                                </div>
                            </div>
                        ");
                    }),

                // === ACTION: EWS DROP OUT RISK CHECK ===
                Action::make('ews_check')
                    ->label('EWS Risk')
                    ->icon('heroicon-o-shield-exclamation')
                    ->color('warning')
                    ->modalHeading('Early Warning System (EWS) Risk Audit')
                    ->modalSubmitAction(false)
                    ->modalContent(function ($record) {
                        $service = app(EarlyWarningService::class);
                        $logs = $service->analyzeStudent($record);

                        if (empty($logs)) {
                            return new HtmlString("<div class='p-4 bg-emerald-50 text-emerald-700 rounded-lg font-bold text-center text-xs'>Mahasiswa dalam kondisi akademik aman (Tidak terdeteksi risiko Drop-Out).</div>");
                        }

                        $logItems = '';
                        foreach ($logs as $log) {
                            $logItems .= "
                                <div class='p-3 bg-amber-50 border border-amber-200 rounded-lg text-xs space-y-1 mb-2'>
                                    <div class='font-bold text-amber-900'>[{$log->risk_level}] {$log->trigger_criteria}</div>
                                    <div class='text-amber-800'>{$log->description}</div>
                                </div>
                            ";
                        }

                        return new HtmlString("<div class='space-y-2'>{$logItems}</div>");
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