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
            PagesExportAction::make('export')

        ];
    }
}
