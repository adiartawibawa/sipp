<?php

namespace App\Filament\Clusters\Schools\Resources\InfraDocumentResource\Actions;

use Filament\Tables\Actions\BulkAction;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class BulkDownloadAction extends BulkAction
{
    public static function make(?string $name = null): static
    {
        return parent::make($name)
            ->label('Unduh Terpilih')
            ->icon('heroicon-o-arrow-down-tray')
            ->action(function (Collection $records) {
                $zip = new ZipArchive;
                $zipPath = storage_path('app/temp/docs_' . now()->timestamp . '.zip');

                if ($zip->open($zipPath, ZipArchive::CREATE) === TRUE) {
                    foreach ($records as $record) {
                        $filePath = storage_path('app/public/' . $record->path);
                        if (file_exists($filePath)) {
                            $zip->addFile($filePath, $record->name . '.' . $record->extension);
                        }
                    }
                    $zip->close();
                }

                return response()->download($zipPath)->deleteFileAfterSend();
            })
            ->requiresConfirmation()
            ->modalDescription('Dokumen terpilih akan dikompresi dalam format ZIP')
            ->modalSubmitActionLabel('Unduh Sekarang');
    }
}
