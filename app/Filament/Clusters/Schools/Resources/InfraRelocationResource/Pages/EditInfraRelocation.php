<?php

namespace App\Filament\Clusters\Schools\Resources\InfraRelocationResource\Pages;

use App\Filament\Clusters\Schools\Resources\InfraRelocationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInfraRelocation extends EditRecord
{
    protected static string $resource = InfraRelocationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
