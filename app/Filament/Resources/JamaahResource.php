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
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\JamaahResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
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
                        ->numeric()
                        ->length(16)
                        ->placeholder('NIK')
                        ->unique(ignorable: fn($record) => $record)
                        ->required(),
                    Textarea::make('alamat')
                        ->label('Alamat')
                        ->placeholder('Masukkan alamat')
                        ->required(),

                    Select::make('provinsi')
                        ->label('Provinsi')
                        ->options(collect(\Indonesia::allProvinces())->pluck('name', 'id')->toArray()) // Ambil data provinsi dari Indonesia package
                        ->reactive() // Tambahkan reaktivitas untuk memengaruhi pilihan kota
                        ->required()
                        ->placeholder('Pilih Provinsi'),

                    Select::make('kota')
                        ->label('Kabupaten/Kota')
                        ->options(function (callable $get) {
                            // Ambil pilihan kota berdasarkan provinsi yang dipilih
                            $selectedProvinsi = $get('provinsi'); // Mengambil nilai dari provinsi yang dipilih
                            if ($selectedProvinsi) {
                                // Ambil data kota dari package Indonesia berdasarkan provinsi
                                return collect(\Indonesia::findProvince($selectedProvinsi)->cities)->pluck('name', 'id')->toArray();
                            }
                            return []; // Default kosong jika belum ada provinsi yang dipilih
                        })
                        ->reactive() // Reaktif untuk memperbarui kecamatan
                        ->required()
                        ->placeholder('Pilih Kabupaten/Kota'),

                    Select::make('kecamatan')
                        ->label('Kecamatan')
                        ->options(function (callable $get) {
                            // Ambil pilihan kecamatan berdasarkan kota yang dipilih
                            $selectedKota = $get('kota'); // Mengambil nilai dari kota yang dipilih
                            if ($selectedKota) {
                                // Ambil data kecamatan dari package Indonesia berdasarkan kota
                                return collect(\Indonesia::findCity($selectedKota)->districts)->pluck('name', 'id')->toArray();
                            }
                            return []; // Default kosong jika belum ada kota yang dipilih
                        })
                        ->reactive() // Reaktif untuk memperbarui kelurahan
                        ->required()
                        ->placeholder('Pilih Kecamatan'),

                    Select::make('kelurahan')
                        ->label('Desa/Kelurahan')
                        ->options(function (callable $get) {
                            // Ambil pilihan kelurahan berdasarkan kecamatan yang dipilih
                            $selectedKecamatan = $get('kecamatan'); // Mengambil nilai dari kecamatan yang dipilih
                            if ($selectedKecamatan) {
                                // Ambil data kelurahan dari package Indonesia berdasarkan kecamatan
                                return collect(\Indonesia::findDistrict($selectedKecamatan)->villages)->pluck('name', 'id')->toArray();
                            }
                            return []; // Default kosong jika belum ada kecamatan yang dipilih
                        })
                        ->required()
                        ->placeholder('Pilih Desa/Kelurahan'),
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
                TextColumn::make('provinsi')
                    ->label('Provinsi')
                    ->toggleable()
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(function ($state) {
                        // Ambil nama provinsi berdasarkan ID yang disimpan
                        return \Indonesia::findProvince($state)?->name ?? 'Tidak ditemukan';
                    }),
                TextColumn::make('kota')
                    ->label('Kota')
                    ->toggleable()
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(function ($state) {
                        // Ambil nama provinsi berdasarkan ID yang disimpan
                        return \Indonesia::findCity($state)?->name ?? 'Tidak ditemukan';
                    }),
                TextColumn::make('kecamatan')
                    ->label('Kota')
                    ->toggleable()
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(function ($state) {
                        // Ambil nama provinsi berdasarkan ID yang disimpan
                        return \Indonesia::findDistrict($state)?->name ?? 'Tidak ditemukan';
                    }),
                TextColumn::make('kelurahan')
                    ->label('Kota')
                    ->toggleable()
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(function ($state) {
                        // Ambil nama provinsi berdasarkan ID yang disimpan
                        return \Indonesia::findVillage($state)?->name ?? 'Tidak ditemukan';
                    }),
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
                    ExportBulkAction::make()
                ]),
                // ExportBulkAction::make()
            ])
            ->recordUrl(
                fn(Model $record): string => Pages\ViewJamaah::getUrl([$record->id]),
            );;
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    // public function getTableBulkActions()
    // {
    //     return  [
    //         ExportBulkAction::make()
    //     ];
    // }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListJamaahs::route('/'),
            'create' => Pages\CreateJamaah::route('/create'),
            'edit' => Pages\EditJamaah::route('/{record}/edit'),
            'view' => Pages\ViewJamaah::route('/{record}/view'),
        ];
    }
}
