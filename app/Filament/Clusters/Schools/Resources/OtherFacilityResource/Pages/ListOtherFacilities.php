<?php

namespace App\Filament\Clusters\Schools\Resources\OtherFacilityResource\Pages;

use App\Filament\Clusters\Schools\Resources\OtherFacilityResource;
use App\Filament\Clusters\Schools\Resources\OtherFacilityResource\Widgets\OtherFacilityStats;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOtherFacilities extends ListRecords
{
    protected static string $resource = OtherFacilityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            OtherFacilityStats::class,
        ];
    }
}
