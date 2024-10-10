<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Jamaah;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\JamaahResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\JamaahResource\RelationManagers;

class JamaahResource extends Resource
{
    protected static ?string $model = Jamaah::class;

    protected static ?string $recordTitleAttribute = 'nama';
    protected static ?string $modelLabel = 'Jamaah';
    protected static ?string $navigationLabel = 'Data Jamaah';
    protected static ?string $breadcrumb = 'Jamaah';
    protected static ?string $title = 'Jamaah';
    protected static ?string $pluralModelLabel = 'Jamaah';
    protected static ?string $navigationIcon = 'heroicon-o-user';
    // protected static ?string $navigationGroup = 'Manajemen Jamaah';
    protected static ?string $slug = 'jamaah';
    protected static ?int $navigationSort = 1;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make([
                    TextInput::make('nama')
                        ->label('Nama')
                        ->placeholder('Masukkan nama lengkap')
                        ->required(),
                    TextInput::make('nik')
                        ->label('NIK')
                        ->integer()
                        ->length(16)
                        ->placeholder('NIK')
                        ->unique(ignorable: fn($record) => $record)
                        ->required(),
                    TextInput::make('alamat')
                        ->label('Alamat')
                        ->placeholder('Masukkan alamat')
                        ->unique(ignorable: fn($record) => $record)
                        ->required(),
                    DatePicker::make('tanggal_lahir')
                        ->required(),
                    TextInput::make('tempat_lahir')
                        ->label('Tempat Lahir')
                        ->placeholder('Masukkan tempat lahir')
                        ->required(),
                    Radio::make('jenis_kelamin')->options([
                        'Laki-laki' => 'Laki-laki',
                        'Perempuan' => 'Perempuan',
                    ])->required(),
                    TextInput::make('no_paspor')
                        ->label('Nomor Paspor')
                        ->placeholder('Masukkan nomor paspor')
                        ->unique(ignorable: fn($record) => $record)
                        ->required(),
                    DatePicker::make('masa_berlaku_paspor')
                        ->required(),

                ])->columns(1),

                Section::make([
                    FileUpload::make('ktp')
                        ->image()
                        ->label('Upload KTP (Max. 2 MB)')
                        ->directory('files')
                        ->columnSpan(2)
                        ->required(),
                    FileUpload::make('kk')
                        ->image()
                        ->label('Upload Kartu Keluarga (Max. 2 MB)')
                        ->directory('files')
                        ->columnSpan(2)
                        ->required(),
                    FileUpload::make('foto')
                        ->image()
                        ->label('Upload Foto Diri (Max. 2 MB)')
                        ->directory('files')
                        ->columnSpan(2)
                        ->required(),
                    FileUpload::make('paspor')
                        ->image()
                        ->label('Upload Paspor (Max. 2 MB)')
                        ->directory('files')
                        ->columnSpan(2)
                        ->required(),
                ])->columns(2),
                Section::make([
                    TextInput::make('no_visa')
                        ->label('Nomor Visa')
                        ->placeholder('Masukkan nomor visa')
                        ->unique(ignorable: fn($record) => $record),
                    DatePicker::make('masa_berlaku_visa')
                ])->columns(1),
                Section::make([
                    Select::make('paket')->options([
                        'Paket Itikaf' => 'Paket Itikaf',
                        'Paket Reguler' => 'Paket Reguler',
                        'Paket VIP' => 'Paket VIP',

                    ])->required(),
                    Select::make('kamar')->options([
                        'Quint' => 'Quint',
                        'Quad' => 'Quad',
                        'Triple' => 'Triple',
                        'Double' => 'Double',
                        'Single' => 'Single',
                    ])->required(),
                ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama')
                    ->label('Nama')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('nik')
                    ->label('NIK')
                    ->toggleable()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('alamat')
                    ->label('Alamat')
                    ->toggleable()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('tempat_lahir')
                    ->label('Tempat Lahir')
                    ->toggleable()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('tanggal_lahir')
                    ->label('Tanggal Lahir')
                    ->toggleable()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('jenis_kelamin')
                    ->label('Jenis Kelamin')
                    ->toggleable()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('no_paspor')
                    ->label('Nomor Paspor')
                    ->toggleable()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('masa_berlaku_paspor')
                    ->label('Masa Berlaku Paspor')
                    ->toggleable()
                    ->sortable()
                    ->searchable(),
                ImageColumn::make('ktp'),
                ImageColumn::make('kk'),
                ImageColumn::make('foto'),
                ImageColumn::make('paspor'),
                TextColumn::make('paket')
                    ->label('Paket')
                    ->toggleable()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('kamar')
                    ->label('Kamar')
                    ->toggleable()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('no_visa')
                    ->label('Nomor Visa')
                    ->toggleable()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('masa_berlaku_visa')
                    ->label('Masa Berlaku Visa')
                    ->toggleable()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->toggleable()
                    ->sortable()
                    ->dateTime('d-m-Y H:i:s'),
                TextColumn::make('updated_at')
                    ->label('Diubah Pada')
                    ->toggleable()
                    ->sortable()
                    ->dateTime('d-m-Y H:i:s'),

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListJamaahs::route('/'),
            'create' => Pages\CreateJamaah::route('/create'),
            'edit' => Pages\EditJamaah::route('/{record}/edit'),
        ];
    }
}
