<?php

namespace App\Filament\Clusters\Schools\Resources\LandResource\Pages;

use App\Filament\Clusters\Schools\Resources\LandResource;
use App\Filament\Clusters\Schools\Resources\LandResource\Widgets\LandStatsWidget;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLands extends ListRecords
{
    protected static string $resource = LandResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            LandStatsWidget::class,
        ];
    }
}
