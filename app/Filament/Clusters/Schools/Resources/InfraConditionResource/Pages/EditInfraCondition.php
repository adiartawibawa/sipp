<?php

namespace App\Filament\Clusters\Schools\Resources\InfraConditionResource\Pages;

use App\Filament\Clusters\Schools\Resources\InfraConditionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInfraCondition extends EditRecord
{
    protected static string $resource = InfraConditionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
