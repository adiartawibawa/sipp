<?php

namespace App\Filament\Clusters\Schools\Resources\InfraAcquisitionResource\Pages;

use App\Filament\Clusters\Schools\Resources\InfraAcquisitionResource;
use App\Filament\Clusters\Schools\Resources\InfraAcquisitionResource\Widgets\InfraAcquisitionStats;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListInfraAcquisitions extends ListRecords
{
    protected static string $resource = InfraAcquisitionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            InfraAcquisitionStats::class,
        ];
    }
}
