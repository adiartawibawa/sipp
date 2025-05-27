<?php

namespace App\Filament\Clusters\Schools\Resources\InfraCategoryResource\Pages;

use App\Filament\Clusters\Schools\Resources\InfraCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInfraCategory extends EditRecord
{
    protected static string $resource = InfraCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
