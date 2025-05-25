<?php

namespace App\Filament\Clusters\Administrasi\Resources\LogActivityResource\Pages;

use App\Filament\Clusters\Administrasi\Resources\LogActivityResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageActivityLogs extends ManageRecords
{
    protected static string $resource = LogActivityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
