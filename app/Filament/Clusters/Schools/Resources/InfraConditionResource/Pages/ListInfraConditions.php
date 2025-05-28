<?php

namespace App\Filament\Clusters\Schools\Resources\InfraConditionResource\Pages;

use App\Filament\Clusters\Schools\Resources\InfraConditionResource;
use App\Filament\Clusters\Schools\Resources\InfraConditionResource\Widgets\InfraConditionChart;
use App\Filament\Clusters\Schools\Resources\InfraConditionResource\Widgets\InfraConditionStats;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListInfraConditions extends ListRecords
{
    protected static string $resource = InfraConditionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            InfraConditionStats::class,
            // InfraConditionChart::class,
        ];
    }
}
