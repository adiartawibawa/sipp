<?php

namespace App\Filament\Clusters\Schools\Resources\InfraDocumentResource\Pages;

use App\Filament\Clusters\Schools\Resources\InfraDocumentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInfraDocument extends EditRecord
{
    protected static string $resource = InfraDocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
