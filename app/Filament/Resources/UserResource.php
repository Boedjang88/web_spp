<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users'; // Ganti icon jadi User
    protected static ?string $navigationLabel = 'Manajemen Petugas';
    protected static ?string $navigationGroup = 'Pengaturan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Nama Petugas')
                    ->required()
                    ->maxLength(255),

                TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),

                // Pilihan Role (Admin / Petugas)
                Select::make('role')
                    ->label('Jabatan / Role')
                    ->options([
                        'admin' => 'Administrator (Full Akses)',
                        'petugas' => 'Petugas TU (Input Only)',
                    ])
                    ->required()
                    ->default('petugas'),

                // Input Password Canggih
                TextInput::make('password')
                    ->password()
                    ->label('Password')
                    // Hash password otomatis sebelum simpan ke DB
                    ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                    // Kalau edit dan password kosong, jangan diupdate
                    ->dehydrated(fn ($state) => filled($state))
                    // Wajib diisi cuma pas bikin user baru
                    ->required(fn (string $context): bool => $context === 'create'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                    
                TextColumn::make('email')
                    ->searchable(),
                
                // Badge Role Biar Keren
                TextColumn::make('role')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'admin' => 'danger',   // Merah
                        'petugas' => 'info',   // Biru
                        default => 'gray',
                    }),
                    
                TextColumn::make('created_at')
                    ->dateTime('d M Y')
                    ->label('Dibuat Tgl'),
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}