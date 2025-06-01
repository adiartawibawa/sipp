<?php

namespace App\Filament\Clusters\Schools\Resources\FacilityConditionResource\Pages;

use App\Filament\Clusters\Schools\Resources\FacilityConditionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFacilityCondition extends EditRecord
{
    protected static string $resource = FacilityConditionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
