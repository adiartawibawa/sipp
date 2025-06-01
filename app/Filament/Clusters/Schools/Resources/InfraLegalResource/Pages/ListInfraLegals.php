<?php

namespace App\Filament\Clusters\Schools\Resources\InfraLegalResource\Pages;

use App\Filament\Clusters\Schools\Resources\InfraLegalResource;
use App\Filament\Clusters\Schools\Resources\InfraLegalResource\Widgets\InfraLegalStats;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListInfraLegals extends ListRecords
{
    protected static string $resource = InfraLegalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            InfraLegalStats::class,
        ];
    }
}
