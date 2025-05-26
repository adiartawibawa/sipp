<?php

namespace App\Filament\Clusters\Administrasi\Resources\VillageResource\Pages;

use App\Filament\Clusters\Administrasi\Resources\VillageResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVillage extends EditRecord
{
    protected static string $resource = VillageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
