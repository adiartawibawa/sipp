<?php

namespace App\Filament\Clusters\Schools\Resources\RoomReferenceResource\Pages;

use App\Filament\Clusters\Schools\Resources\RoomReferenceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRoomReference extends EditRecord
{
    protected static string $resource = RoomReferenceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
