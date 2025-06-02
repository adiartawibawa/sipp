<?php

namespace App\Filament\Clusters\Schools\Resources\InfraConditionResource\Pages;

use App\Filament\Clusters\Schools\Resources\InfraConditionResource;
use App\Filament\Clusters\Schools\Resources\InfraConditionResource\Widgets\InfraConditionDistribution;
use App\Filament\Clusters\Schools\Resources\InfraConditionResource\Widgets\InfraConditionStats;
use App\Filament\Clusters\Schools\Resources\InfraConditionResource\Widgets\InfraConditionTrend;
use App\Filament\Clusters\Schools\Resources\InfraConditionResource\Widgets\RecentInfraConditions;
use App\Filament\Clusters\Schools\Widgets\InfraConditionStatsWidget;
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
            RecentInfraConditions::class,
            InfraConditionDistribution::class,
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            InfraConditionTrend::class,
        ];
    }
}
