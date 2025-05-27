<?php

namespace App\Filament\Clusters\Schools\Resources\OtherFacilityResource\Pages;

use App\Filament\Clusters\Schools\Resources\OtherFacilityResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOtherFacility extends EditRecord
{
    protected static string $resource = OtherFacilityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
