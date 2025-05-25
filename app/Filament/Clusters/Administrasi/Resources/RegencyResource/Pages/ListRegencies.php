<?php

namespace App\Filament\Clusters\Administrasi\Resources\RegencyResource\Pages;

use App\Filament\Clusters\Administrasi\Resources\RegencyResource;
use App\Filament\Clusters\Administrasi\Resources\RegencyResource\Actions\ImportRegenciesAction;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRegencies extends ListRecords
{
    protected static string $resource = RegencyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            ImportRegenciesAction::make('importRegencies')
                ->label('Import Regencies')
                ->outlined()
                ->icon('heroicon-o-cloud-arrow-up'),
        ];
    }
}
