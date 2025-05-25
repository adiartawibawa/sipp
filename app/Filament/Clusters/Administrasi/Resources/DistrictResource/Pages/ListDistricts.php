<?php

namespace App\Filament\Clusters\Administrasi\Resources\DistrictResource\Pages;

use App\Filament\Clusters\Administrasi\Resources\DistrictResource;
use App\Filament\Clusters\Administrasi\Resources\DistrictResource\Actions\ImportDistrictsAction;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDistricts extends ListRecords
{
    protected static string $resource = DistrictResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            ImportDistrictsAction::make('importDistrict')
                ->label('Import Kecamatan')
                ->outlined()
                ->icon('heroicon-o-cloud-arrow-up'),
        ];
    }
}
