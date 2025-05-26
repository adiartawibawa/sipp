<?php

namespace App\Filament\Clusters\Administrasi\Resources\VillageResource\Pages;

use App\Filament\Clusters\Administrasi\Resources\VillageResource;
use App\Filament\Clusters\Administrasi\Resources\VillageResource\Actions\ImportVillagesAction;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListVillages extends ListRecords
{
    protected static string $resource = VillageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            ImportVillagesAction::make('importVillages')
                ->label('Import Villages')
                ->outlined()
                ->icon('heroicon-o-cloud-arrow-up'),
        ];
    }
}
