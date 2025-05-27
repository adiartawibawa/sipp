<?php

namespace App\Filament\Clusters\Schools\Resources\RoomResource\Pages;

use App\Filament\Clusters\Schools\Resources\RoomResource;
use App\Filament\Clusters\Schools\Resources\RoomResource\Widgets\RoomStatsOverview;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRooms extends ListRecords
{
    protected static string $resource = RoomResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            RoomStatsOverview::class,
        ];
    }
}
