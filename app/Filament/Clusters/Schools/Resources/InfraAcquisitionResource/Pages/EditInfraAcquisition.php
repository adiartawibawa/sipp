<?php

namespace App\Filament\Clusters\Schools\Resources\InfraAcquisitionResource\Pages;

use App\Filament\Clusters\Schools\Resources\InfraAcquisitionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInfraAcquisition extends EditRecord
{
    protected static string $resource = InfraAcquisitionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
