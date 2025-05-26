<?php

namespace App\Filament\Clusters\Administrasi\Resources\DistrictResource\Actions;

use App\Models\Regions\District;
use App\Services\CsvProcessor;
use Exception;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImportDistrictsAction extends Action
{
    public static function make(?string $name = null): static
    {
        return parent::make($name)
            ->label('Import CSV')
            ->icon('heroicon-o-arrow-up-tray')
            ->color('success')
            ->modalHeading('Import Districts from CSV')
            ->modalDescription('Upload a CSV file with district data. The file should contain columns: id, regency_id, name, lat, lon')
            ->modalSubmitActionLabel('Import')
            ->form([
                FileUpload::make('file')
                    ->label('CSV File')
                    ->required()
                    ->acceptedFileTypes([
                        'text/csv',
                        'text/plain',
                        'application/csv',
                        'text/comma-separated-values',
                        'application/vnd.ms-excel',
                    ])
                    ->directory('district-imports')
                    ->preserveFilenames()
                    ->maxSize(1024)
                    ->helperText('Format kolom: id, regency_id, name, lat, lon')
                    ->rules(['file', 'mimetypes:text/csv,text/plain']),
            ])
            ->action(function (array $data) {
                try {
                    // Dapatkan path file fisik dari storage
                    $filePath = Storage::disk('public')->path($data['file']);

                    // Pastikan file ada dan bisa dibaca
                    if (!Storage::disk('public')->exists($data['file'])) {
                        throw new Exception("The uploaded file could not be found.");
                    }

                    if (!is_readable($filePath)) {
                        throw new Exception("The uploaded file is not readable.");
                    }

                    $csvProcessor = app(CsvProcessor::class);

                    // Proses CSV dengan header dan chunking
                    $rows = $csvProcessor->process($filePath, [
                        'header' => ['id', 'regency_id', 'name', 'lat', 'lon'],
                        'skipHeader' => true,
                        'chunkSize' => 500,
                    ]);

                    $totalImported = 0;
                    $allErrors = [];

                    foreach ($rows as $chunkIndex => $chunk) {
                        DB::beginTransaction();
                        $importedInChunk = 0;
                        $chunkErrors = [];

                        foreach ($chunk as $index => $row) {
                            try {
                                self::importRow($row);
                                $importedInChunk++;
                            } catch (Exception $e) {
                                $chunkErrors[] = "Chunk {$chunkIndex}, Row {$index}: " . $e->getMessage();
                                Log::error("District import error", ['row' => $row, 'error' => $e->getMessage()]);
                                continue;
                            }
                        }

                        // Commit transaction untuk chunk ini
                        DB::commit();
                        $totalImported += $importedInChunk;
                        $allErrors = array_merge($allErrors, $chunkErrors);
                    }

                    // Tampilkan notifikasi hasil import
                    $message = "Successfully imported {$totalImported} districts.";
                    if (count($allErrors) > 0) {
                        $message .= "\n\n" . count($allErrors) . " rows failed:\n" . implode("\n", array_slice($allErrors, 0, 5));
                        if (count($allErrors) > 5) {
                            $message .= "\n...and " . (count($allErrors) - 5) . " more";
                        }
                    }

                    Notification::make()
                        ->title($totalImported > 0 ? 'Import Completed' : 'Import Failed')
                        ->body($message)
                        ->icon($totalImported > 0 ? 'heroicon-o-check-circle' : 'heroicon-o-exclamation-circle')
                        ->color($totalImported > 0 ? 'success' : 'danger')
                        ->send();
                } catch (Exception $e) {
                    DB::rollBack();
                    Log::error("ImportDistrictsAction failed: " . $e->getMessage());

                    Notification::make()
                        ->title('Import Failed')
                        ->body("Error: " . $e->getMessage())
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->send();

                    throw $e;
                }
            });
    }

    /**
     * Memproses satu baris data untuk diimpor ke database.
     */
    protected static function importRow(array $row): void
    {
        if (empty($row['id']) || empty($row['name'])) {
            throw new Exception("Missing required fields (id or name).");
        }

        District::updateOrCreate(
            ['id' => $row['id']],
            [
                'regency_id' => $row['regency_id'],
                'name' => $row['name'],
                'slug' => Str::slug($row['name']),
                'latitude' => self::parseCoordinate($row['lat'] ?? null),
                'longitude' => self::parseCoordinate($row['lon'] ?? null),
            ]
        );
    }

    /**
     * Konversi nilai koordinat ke float jika valid.
     */
    protected static function parseCoordinate($value): ?float
    {
        return is_numeric($value) ? (float) $value : null;
    }
}
