<?php

namespace App\Filament\Clusters\Administrasi\Resources\VillageResource\Actions;

use App\Models\Regions\Village;
use App\Services\CsvProcessor;
use Exception;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImportVillagesAction extends Action
{
    public static function make(?string $name = null): static
    {
        return parent::make($name)
            ->label('Import CSV')
            ->icon('heroicon-o-arrow-up-tray')
            ->color('success')
            ->modalHeading('Import Villages from CSV')
            ->modalDescription('Upload a CSV file with village data. The file should contain columns: id, district_id, name, lat, lon, postal_code')
            ->modalSubmitActionLabel('Import')
            ->form([
                FileUpload::make('file')
                    ->label('CSV File')
                    ->multiple()
                    ->required()
                    ->acceptedFileTypes([
                        'text/csv',
                        'text/plain',
                        'application/csv',
                        'text/comma-separated-values',
                        'application/vnd.ms-excel',
                    ])
                    ->directory('village-imports')
                    ->preserveFilenames()
                    ->maxSize(1024)
                    ->helperText('Format kolom: id, district_id, name, lat, lon, postal_code')
                    ->rules(['file', 'mimetypes:text/csv,text/plain']),
            ])
            ->action(function (array $data) {
                try {
                    /** @var CsvProcessor $csvProcessor */
                    $csvProcessor = app(CsvProcessor::class);

                    $allFiles = $data['file']; // array of filenames
                    $totalImported = 0;
                    $allErrors = [];

                    foreach ($allFiles as $filename) {
                        $filePath = Storage::disk('public')->path($filename);

                        if (!Storage::disk('public')->exists($filename)) {
                            $allErrors[] = "File not found: {$filename}";
                            continue;
                        }

                        if (!is_readable($filePath)) {
                            $allErrors[] = "File not readable: {$filename}";
                            continue;
                        }

                        $rows = $csvProcessor->process($filePath, [
                            'header' => ['id', 'district_id', 'name', 'lat', 'lon', 'postal_code'],
                            'skipHeader' => true,
                            'chunkSize' => 500,
                        ]);

                        foreach ($rows as $chunkIndex => $chunk) {
                            DB::beginTransaction();
                            $importedInChunk = 0;
                            $chunkErrors = [];

                            foreach ($chunk as $index => $row) {
                                try {
                                    self::importRow($row);
                                    $importedInChunk++;
                                } catch (Exception $e) {
                                    $chunkErrors[] = "File {$filename} - Chunk {$chunkIndex}, Row {$index}: " . $e->getMessage();
                                    Log::error("Village import error", ['row' => $row, 'error' => $e->getMessage()]);
                                }
                            }

                            DB::commit();
                            $totalImported += $importedInChunk;
                            $allErrors = array_merge($allErrors, $chunkErrors);
                        }
                    }

                    // Display notification
                    $message = "Successfully imported {$totalImported} villages.";
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
                    Log::error("ImportVillagesAction failed: " . $e->getMessage());

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

        Village::updateOrCreate(
            ['id' => $row['id']],
            [
                'district_id' => $row['district_id'],
                'name' => $row['name'],
                'slug' => Str::slug($row['name']),
                'postal_code' => Str::slug($row['postal_code']),
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
