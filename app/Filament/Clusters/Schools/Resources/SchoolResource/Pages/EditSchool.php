<?php

namespace App\Filament\Clusters\Schools\Resources\SchoolResource\Pages;

use App\Filament\Clusters\Schools\Resources\SchoolResource;
use App\Filament\Clusters\Schools\Resources\SchoolResource\Widgets\BuildingsStatsOverview;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSchool extends EditRecord
{
    protected static string $resource = SchoolResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            BuildingsStatsOverview::class,
        ];
    }
}
