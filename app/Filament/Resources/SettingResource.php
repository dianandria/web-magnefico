<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SettingResource\Pages;
use App\Filament\Resources\SettingResource\RelationManagers;
use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Model;

class SettingResource extends Resource
{
    protected static ?string $model = Setting::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Others';

    protected static ?int $navigationSort = 6;

    // 1. Matikan fitur Create (Tambah Data)
    public static function canCreate(): bool
    {
        return false;
    }

    // 2. Matikan fitur Delete (Hapus per baris)
    public static function canDelete(Model $record): bool
    {
        return false;
    }

    // 3. Matikan fitur Bulk Delete (Hapus massal yang ada di checklist tabel)
    public static function canDeleteAny(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('key')
                    ->required()
                    ->disabled() // Biasakan key tidak bisa diubah admin agar tidak merusak front-end
                    ->maxLength(255),

                Forms\Components\TextInput::make('type')
                    ->required()
                    ->disabled(), // Type juga sebaiknya di-lock

                // --- DYNAMIC VALUE FIELDS ---
                // Gunakan Group dengan evaluasi Closure dinamis
                Forms\Components\Group::make()
                    ->schema(function (?Setting $record) {
                        
                        // Jika $record null (halaman Create), tampilkan input teks biasa
                        if (! $record) {
                            return [
                                Forms\Components\TextInput::make('value')
                                    ->label('Value')
                                    ->required(),
                            ];
                        }

                        // Cek 'type' dari database, lalu render HANYA SATU input yang sesuai
                        return match ($record->type) {
                            'textarea' => [
                                Forms\Components\Textarea::make('value')
                                    ->label('Value')
                                    ->rows(4)
                                    ->required(),
                            ],
                            'image' => [
                                Forms\Components\FileUpload::make('value')
                                    ->label('Image Upload')
                                    ->image()
                                    ->directory('settings')
                                    ->required(),
                            ],
                            'video' => [
                                Forms\Components\FileUpload::make('value')
                                    ->label('Video Upload')
                                    ->acceptedFileTypes(['video/mp4', 'video/ogg', 'video/webm'])
                                    ->maxSize(102400) // 100MB limit (in KB)
                                    ->directory('videos')
                                    ->visibility('public')
                                    ->preserveFilenames()
                            ],
                            default => [ // Untuk 'text' dan tipe lainnya
                                Forms\Components\TextInput::make('value')
                                    ->label('Value')
                                    ->required(),
                            ],
                        };
                    })
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('key')->searchable(),
                Tables\Columns\TextColumn::make('value')->limit(50),
                Tables\Columns\TextColumn::make('type')->badge(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListSettings::route('/'),
            'create' => Pages\CreateSetting::route('/create'),
            'edit' => Pages\EditSetting::route('/{record}/edit'),
        ];
    }
}
