<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KelasResource\Pages;
use App\Models\Kelas;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;

class KelasResource extends Resource
{
    protected static ?string $model = Kelas::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';
    protected static ?string $navigationLabel = 'Data Kelas & Rombel';
    protected static ?string $navigationGroup = 'Akademik';
    protected static ?int $navigationSort = 2;
    protected static ?string $recordTitleAttribute = 'nama_kelas';

    public static function getGloballySearchableAttributes(): array
    {
        return ['nama_kelas', 'kompetensi_keahlian'];
    }

    public static function getGlobalSearchResultTitle(\Illuminate\Database\Eloquent\Model $record): string
    {
        return "Kelas {$record->nama_kelas} ({$record->kompetensi_keahlian})";
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Kelas & Program Studi')
                    ->description('Identitas rombel dan penempatan program studi.')
                    ->icon('heroicon-o-building-office-2')
                    ->columns(2)
                    ->schema([
                        TextInput::make('nama_kelas')
                            ->label('Nama Kelas / Rombel')
                            ->placeholder('Contoh: IF-3A atau TI-1B')
                            ->required()
                            ->maxLength(10),
                        
                        Select::make('kompetensi_keahlian')
                            ->label('Program Studi / Keahlian')
                            ->options([
                                'Rekayasa Perangkat Lunak' => 'Rekayasa Perangkat Lunak (RPL)',
                                'Teknik Komputer Jaringan' => 'Teknik Komputer Jaringan (TKJ)',
                                'Multimedia' => 'Multimedia (MM)',
                                'Akuntansi' => 'Akuntansi (AKL)',
                                'Perkantoran' => 'Perkantoran (OTKP)',
                                'Bisnis Daring Pemasaran' => 'Pemasaran (BDP)',
                            ])
                            ->searchable()
                            ->required(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->striped()
            ->columns([
                TextColumn::make('nama_kelas')
                    ->label('Kelas / Rombel')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('kompetensi_keahlian')
                    ->label('Program Studi')
                    ->searchable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Rekayasa Perangkat Lunak' => 'info',
                        'Teknik Komputer Jaringan' => 'success',
                        default => 'gray',
                    }),
                TextColumn::make('siswas_count')
                    ->counts('siswas')
                    ->label('Total Mahasiswa')
                    ->badge()
                    ->color('primary')
                    ->toggleable(),
            ])
            ->emptyStateHeading('Belum Ada Data Kelas')
            ->emptyStateDescription('Buat rombel kelas baru untuk mulai mendistribusikan mahasiswa ke rombongan belajar.')
            ->emptyStateIcon('heroicon-o-building-office-2')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()->label('Tambah Kelas Baru'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('kompetensi_keahlian')
                    ->label('Filter Program Studi')
                    ->options([
                        'Rekayasa Perangkat Lunak' => 'RPL',
                        'Teknik Komputer Jaringan' => 'TKJ',
                        'Multimedia' => 'MM',
                        'Akuntansi' => 'AKL',
                    ]),
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
    
    public static function getRelations(): array { return []; }
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKelas::route('/'),
            'create' => Pages\CreateKelas::route('/create'),
            'edit' => Pages\EditKelas::route('/{record}/edit'),
        ];
    }
}