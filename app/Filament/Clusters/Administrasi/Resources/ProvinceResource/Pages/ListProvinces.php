<?php

namespace App\Filament\Clusters\Administrasi\Resources\ProvinceResource\Pages;

use App\Filament\Clusters\Administrasi\Resources\ProvinceResource;
use App\Filament\Clusters\Administrasi\Resources\ProvinceResource\Actions\ImportProvincesAction;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProvinces extends ListRecords
{
    protected static string $resource = ProvinceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            ImportProvincesAction::make('importProvinces')
                ->label('Import Provinces')
                ->outlined()
                ->icon('heroicon-o-cloud-arrow-up'),
        ];
    }
}
