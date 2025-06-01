<?php

namespace App\Filament\Clusters\Schools\Resources\FacilityConditionResource\Pages;

use App\Filament\Clusters\Schools\Resources\FacilityConditionResource;
use App\Filament\Clusters\Schools\Resources\FacilityConditionResource\Widgets\FacilityConditionStats;
use App\Filament\Clusters\Schools\Resources\FacilityConditionResource\Widgets\FacilityConditionTrend;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFacilityConditions extends ListRecords
{
    protected static string $resource = FacilityConditionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            FacilityConditionStats::class,
            FacilityConditionTrend::class,
        ];
    }
}
