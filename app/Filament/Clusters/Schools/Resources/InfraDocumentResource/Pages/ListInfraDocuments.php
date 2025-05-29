<?php

namespace App\Filament\Clusters\Schools\Resources\InfraDocumentResource\Pages;

use App\Filament\Clusters\Schools\Resources\InfraDocumentResource;
use App\Filament\Clusters\Schools\Resources\InfraDocumentResource\Widgets\DocumentStatsWidget;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListInfraDocuments extends ListRecords
{
    protected static string $resource = InfraDocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            DocumentStatsWidget::class,
        ];
    }
}
