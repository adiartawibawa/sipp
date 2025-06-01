<?php

namespace App\Filament\Clusters\Schools\Resources\InfraConditionResource\Pages;

use App\Filament\Clusters\Schools\Resources\InfraConditionResource;
<<<<<<< HEAD
use App\Filament\Clusters\Schools\Resources\InfraConditionResource\Widgets\InfraConditionChart;
use App\Filament\Clusters\Schools\Resources\InfraConditionResource\Widgets\InfraConditionStats;
=======
use App\Filament\Clusters\Schools\Resources\InfraConditionResource\Widgets\InfraConditionStats;
use App\Filament\Clusters\Schools\Resources\InfraConditionResource\Widgets\InfraConditionTrend;
>>>>>>> e8c8ba3ba3e9e4a4d8b5d4b74c2ea726dc0d0153
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
<<<<<<< HEAD
            // InfraConditionChart::class,
=======
            InfraConditionTrend::class,
>>>>>>> e8c8ba3ba3e9e4a4d8b5d4b74c2ea726dc0d0153
        ];
    }
}
