<?php

namespace App\Filament\Clusters\Administrasi\Resources\DistrictResource\Pages;

use App\Filament\Clusters\Administrasi\Resources\DistrictResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDistrict extends EditRecord
{
    protected static string $resource = DistrictResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
