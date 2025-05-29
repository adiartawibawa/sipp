<?php

namespace App\Filament\Clusters\Schools\Resources\InfraLegalResource\Actions;

use App\Models\Schools\InfraLegal;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Storage;
// use Barryvdh\DomPDF\Facade\Pdf;

class GenerateLegalDocumentAction extends Action
{
    public static function make(?string $name = null): static
    {
        return parent::make($name)
            ->label('Generate Dokumen')
            ->icon('heroicon-o-document-arrow-down')
            ->color('success')
            // ->action(function (InfraLegal $record, array $data) {
            //     $pdf = Pdf::loadView('pdf.legal-document', [
            //         'record' => $record,
            //         'data' => $data,
            //     ]);

            //     $filename = "legal-document-{$record->id}.pdf";
            //     $path = "legal-documents/{$filename}";

            //     Storage::put($path, $pdf->output());

            //     return response()->download(storage_path("app/{$path}"))->deleteFileAfterSend();
            // })
            ->form([
                TextInput::make('title')
                    ->label('Judul Dokumen')
                    ->required()
                    ->default(fn(InfraLegal $record) => "Sertifikat {$record->status}"),

                Textarea::make('additional_notes')
                    ->label('Catatan Tambahan'),
            ]);
    }
}
