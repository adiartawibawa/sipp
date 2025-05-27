<?php

namespace App\Filament\Clusters\Schools\Resources\BuildingResource\Pages;

use App\Filament\Clusters\Schools\Resources\BuildingResource;
use App\Filament\Clusters\Schools\Resources\BuildingResource\Widgets\BuildingCondition;
use App\Filament\Clusters\Schools\Resources\BuildingResource\Widgets\BuildingStatsOverview;
use App\Filament\Clusters\Schools\Resources\BuildingResource\Widgets\BuildingTypeChart;
use App\Filament\Clusters\Schools\Resources\BuildingResource\Widgets\BuildingYearlyGrowth;
use App\Filament\Clusters\Schools\Resources\BuildingResource\Widgets\RecentBuildings;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBuildings extends ListRecords
{
    protected static string $resource = BuildingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            BuildingStatsOverview::class,
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            BuildingTypeChart::class,
            // BuildingCondition::class,
            // BuildingYearlyGrowth::class,
            RecentBuildings::class,
        ];
    }
}
