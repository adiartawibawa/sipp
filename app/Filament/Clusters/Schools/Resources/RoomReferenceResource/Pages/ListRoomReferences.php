<?php

namespace App\Filament\Clusters\Schools\Resources\RoomReferenceResource\Pages;

use App\Filament\Clusters\Schools\Resources\RoomReferenceResource;
use App\Filament\Clusters\Schools\Resources\RoomReferenceResource\Widgets\RoomReferenceStats;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRoomReferences extends ListRecords
{
    protected static string $resource = RoomReferenceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            RoomReferenceStats::class,
        ];
    }
}
