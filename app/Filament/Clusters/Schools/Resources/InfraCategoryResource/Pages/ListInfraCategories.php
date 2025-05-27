<?php

namespace App\Filament\Clusters\Schools\Resources\InfraCategoryResource\Pages;

use App\Filament\Clusters\Schools\Resources\InfraCategoryResource;
use App\Filament\Clusters\Schools\Resources\InfraCategoryResource\Widgets\InfraCategoryStats;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListInfraCategories extends ListRecords
{
    protected static string $resource = InfraCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            InfraCategoryStats::class,
        ];
    }
}
