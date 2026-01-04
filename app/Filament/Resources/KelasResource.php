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
    protected static ?string $navigationLabel = 'Data Kelas';
    
    // BIAR RAPI DI SIDEBAR
    protected static ?string $navigationGroup = 'Data Master';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('nama_kelas')
                    ->label('Nama Kelas')
                    ->placeholder('Contoh: XII RPL 1')
                    ->required()
                    ->maxLength(10),
                
                Select::make('kompetensi_keahlian')
                    ->label('Jurusan')
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
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama_kelas')->label('Kelas')->searchable()->sortable(),
                TextColumn::make('kompetensi_keahlian')
                    ->label('Jurusan')
                    ->searchable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Rekayasa Perangkat Lunak' => 'info',
                        'Teknik Komputer Jaringan' => 'success',
                        default => 'gray',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('kompetensi_keahlian')
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