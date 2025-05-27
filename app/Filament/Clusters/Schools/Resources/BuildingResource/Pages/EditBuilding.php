<?php

namespace App\Filament\Clusters\Schools\Resources\BuildingResource\Pages;

use App\Filament\Clusters\Schools\Resources\BuildingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBuilding extends EditRecord
{
    protected static string $resource = BuildingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
