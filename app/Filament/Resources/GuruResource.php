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
    protected static ?string $navigationGroup = 'Civitas Akademika';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('nip')
                    ->label('NIDN / NIP')
                    ->required()
                    ->maxLength(20),

                TextInput::make('nama_guru')
                    ->label('Nama Dosen & Gelar')
                    ->required()
                    ->maxLength(100),

                Select::make('jenis_kelamin')
                    ->label('Jenis Kelamin')
                    ->options([
                        'L' => 'Laki-laki',
                        'P' => 'Perempuan',
                    ])
                    ->required(),

                TextInput::make('no_telp')
                    ->label('No. Telepon')
                    ->tel()
                    ->maxLength(20),

                TextInput::make('email')
                    ->label('Email Dosen')
                    ->email()
                    ->maxLength(100),

                Textarea::make('alamat')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nip')->label('NIDN / NIP')->searchable()->sortable(),
                TextColumn::make('nama_guru')->label('Nama Dosen')->searchable()->sortable(),
                TextColumn::make('jenis_kelamin')
                    ->label('L/P')
                    ->formatStateUsing(fn ($state) => $state === 'L' ? 'Laki-laki' : 'Perempuan')
                    ->badge()
                    ->color(fn ($state) => $state === 'L' ? 'info' : 'danger'),
                TextColumn::make('email')->label('Email')->searchable(),
                TextColumn::make('no_telp')->label('Telepon'),
                TextColumn::make('jadwals_count')
                    ->counts('jadwals')
                    ->label('Total Kelas Ajar')
                    ->badge()
                    ->color('success'),
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
