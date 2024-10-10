<?php

namespace App\Filament\Resources\JamaahResource\Pages;

use Filament\Actions;
use Actions\Pages\ExportAction;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\JamaahResource;
use pxlrbt\FilamentExcel\Actions\Pages\ExportAction as PagesExportAction;
// use pxlrbt\FilamentExcel\Actions\Tables\ExportAction as TablesExportAction;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Columns\Column;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class ListJamaahs extends ListRecords
{
    protected static string $resource = JamaahResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            PagesExportAction::make()->exports([
                ExcelExport::make()->withColumns([
                    Column::make('id'),
                    Column::make('nama'),
                    Column::make('nik')->formatStateUsing(fn($state) => strval($state)),
                    Column::make('alamat'),
                    Column::make('provinsi')->formatStateUsing(fn($state) => \Indonesia::findProvince($state)?->name ?? $state),
                    Column::make('kota')->formatStateUsing(fn($state) => \Indonesia::findCity($state)?->name ?? $state),
                    Column::make('kecamatan')->formatStateUsing(fn($state) => \Indonesia::findDistrict($state)?->name ?? $state),
                    Column::make('kelurahan')->formatStateUsing(fn($state) => \Indonesia::findVillage($state)?->name ?? $state),
                    Column::make('tempat_lahir'),
                    Column::make('tanggal_lahir'),
                    Column::make('jenis_kelamin'),
                    Column::make('no_paspor'),
                    Column::make('masa_berlaku_paspor'),
                    Column::make('no_visa'),
                    Column::make('masa_berlaku_visa'),
                    Column::make('ktp'),
                    Column::make('kk'),
                    Column::make('foto'),
                    Column::make('paspor'),
                    Column::make('paket'),
                    Column::make('kamar'),
                    Column::make('created_at'),
                    Column::make('updated_at'),
                ])
            ])
        ];
    }
}
