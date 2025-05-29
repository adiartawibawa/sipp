<?php

namespace App\Filament\Clusters\Schools\Resources\InfraRelocationResource\Pages;

use App\Filament\Clusters\Schools\Resources\InfraRelocationResource;
use App\Filament\Clusters\Schools\Resources\InfraRelocationResource\Widgets\InfraRelocationStats;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListInfraRelocations extends ListRecords
{
    protected static string $resource = InfraRelocationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            InfraRelocationStats::class,
        ];
    }
}
