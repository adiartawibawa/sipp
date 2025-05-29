<?php

namespace App\Filament\Clusters\Schools\Resources\InfraLegalResource\Pages;

use App\Filament\Clusters\Schools\Resources\InfraLegalResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInfraLegal extends EditRecord
{
    protected static string $resource = InfraLegalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
