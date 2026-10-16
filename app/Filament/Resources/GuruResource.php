<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GuruResource\Pages;
use App\Models\Guru;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;

class GuruResource extends Resource
{
    protected static ?string $model = Guru::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    protected static ?string $navigationLabel = 'Data Dosen Pengajar';
    protected static ?string $navigationGroup = 'Akademik';
    protected static ?int $navigationSort = 3;
    protected static ?string $recordTitleAttribute = 'nama_guru';

    public static function getGloballySearchableAttributes(): array
    {
        return ['nip', 'nama_guru', 'email', 'no_telp'];
    }

    public static function getGlobalSearchResultTitle(\Illuminate\Database\Eloquent\Model $record): string
    {
        return "{$record->nama_guru} ({$record->nip})";
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Identitas Tenaga Pendidik')
                    ->description('NIDN / NIP, nama lengkap beserta gelar, dan jenis kelamin.')
                    ->icon('heroicon-o-academic-cap')
                    ->columns(2)
                    ->schema([
                        TextInput::make('nip')
                            ->label('NIDN / NIP')
                            ->placeholder('Contoh: 198501012010121001')
                            ->required()
                            ->maxLength(20),

                        TextInput::make('nama_guru')
                            ->label('Nama Dosen & Gelar')
                            ->placeholder('Contoh: Dr. Ir. Budi Santoso, M.Kom.')
                            ->required()
                            ->maxLength(100),

                        Select::make('jenis_kelamin')
                            ->label('Jenis Kelamin')
                            ->options([
                                'L' => 'Laki-laki',
                                'P' => 'Perempuan',
                            ])
                            ->required(),
                    ]),

                Forms\Components\Section::make('Kontak & Domisili')
                    ->description('Informasi kontak aktif dan domisili untuk komunikasi kedinasan.')
                    ->icon('heroicon-o-phone')
                    ->columns(2)
                    ->schema([
                        TextInput::make('email')
                            ->label('Email Dosen')
                            ->email()
                            ->placeholder('dosen@kampus.ac.id')
                            ->maxLength(100),

                        TextInput::make('no_telp')
                            ->label('No. Telepon / WhatsApp')
                            ->tel()
                            ->placeholder('08123456789')
                            ->maxLength(20),

                        Textarea::make('alamat')
                            ->label('Alamat Domisili')
                            ->placeholder('Alamat lengkap tempat tinggal saat ini')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->striped()
            ->columns([
                TextColumn::make('nip')
                    ->label('NIDN / NIP')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('NIP disalin!'),
                TextColumn::make('nama_guru')
                    ->label('Nama Dosen')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('jenis_kelamin')
                    ->label('L/P')
                    ->formatStateUsing(fn ($state) => $state === 'L' ? 'Laki-laki' : 'Perempuan')
                    ->badge()
                    ->color(fn ($state) => $state === 'L' ? 'info' : 'danger'),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('no_telp')
                    ->label('Telepon')
                    ->toggleable(),
                TextColumn::make('jadwals_count')
                    ->counts('jadwals')
                    ->label('Total Kelas Ajar')
                    ->badge()
                    ->color('success')
                    ->toggleable(),
            ])
            ->emptyStateHeading('Belum Ada Data Dosen')
            ->emptyStateDescription('Mulai tambahkan profil dosen pengajar untuk penugasan mata kuliah dan jadwal kelas.')
            ->emptyStateIcon('heroicon-o-academic-cap')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()->label('Tambah Dosen Baru'),
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
            'index' => Pages\ListGurus::route('/'),
            'create' => Pages\CreateGuru::route('/create'),
            'edit' => Pages\EditGuru::route('/{record}/edit'),
        ];
    }
}
