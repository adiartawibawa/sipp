<?php

namespace App\Filament\Clusters\Schools\Resources\SchoolResource\Pages;

use App\Filament\Clusters\Schools\Resources\SchoolResource;
use App\Filament\Clusters\Schools\Resources\SchoolResource\Widgets\RecentSchoolsTable;
use App\Filament\Clusters\Schools\Resources\SchoolResource\Widgets\SchoolFacilitiesChart;
use App\Filament\Clusters\Schools\Resources\SchoolResource\Widgets\SchoolStatsOverview;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSchools extends ListRecords
{
    protected static string $resource = SchoolResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            SchoolStatsOverview::class,
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            SchoolFacilitiesChart::class,
            RecentSchoolsTable::class,
        ];
    }
}
