<?php

namespace App\Filament\Clusters\Administrasi\Resources\ProvinceResource\Actions;

use App\Models\Regions\Province;
use App\Services\CsvProcessor;
use Exception;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImportProvincesAction extends Action
{
    public static function make(?string $name = null): static
    {
        return parent::make($name)
            ->label('Import CSV')
            ->icon('heroicon-o-arrow-up-tray')
            ->color('success')
            ->modalHeading('Import Province from CSV')
            ->modalDescription('Upload a CSV file with province data. Format: id, name, lat, lon')
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
                    ->directory('province-imports')
                    ->preserveFilenames()
                    ->maxSize(1024)
                    ->helperText('Format kolom: id, name, lat, lon')
                    ->rules(['file', 'mimetypes:text/csv,text/plain']),
            ])
            ->action(function (array $data) {
                try {
                    $file = $data['file'];

                    if (!Storage::disk('public')->exists($file)) {
                        throw new Exception("File not found.");
                    }

                    $filePath = Storage::disk('public')->path($file);

                    if (!is_readable($filePath)) {
                        throw new Exception("File is not readable.");
                    }

                    $csvProcessor = app(CsvProcessor::class);
                    $chunks = $csvProcessor->process($filePath, [
                        'header' => ['id', 'name', 'lat', 'lon'],
                        'skipHeader' => true,
                        'chunkSize' => 500,
                    ]);

                    $totalImported = 0;
                    $allErrors = [];

                    $chunks->each(function ($chunk, $chunkIndex) use (&$totalImported, &$allErrors) {
                        DB::beginTransaction();

                        try {
                            foreach ($chunk as $rowIndex => $row) {
                                try {
                                    self::importRow($row, $rowIndex);
                                    $totalImported++;
                                } catch (Exception $e) {
                                    $errorMessage = "Row {$rowIndex}: " . $e->getMessage();
                                    $allErrors[] = $errorMessage;
                                    Log::error('[Province Import] ' . $errorMessage, ['row' => $row]);
                                }
                            }

                            DB::commit();
                        } catch (Exception $e) {
                            DB::rollBack();
                            Log::critical("[Province Import] Failed to import chunk {$chunkIndex}: " . $e->getMessage());
                            $allErrors[] = "Chunk {$chunkIndex} failed: " . $e->getMessage();
                        }
                    });

                    $message = "Successfully imported {$totalImported} provinces.";
                    if ($allErrors) {
                        $message .= "\n\n" . count($allErrors) . " rows failed:\n" .
                            implode("\n", array_slice($allErrors, 0, 5));

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
                    Notification::make()
                        ->title('Import Failed')
                        ->body("Error: " . $e->getMessage())
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->send();

                    Log::error('[Province Import] Critical Error: ' . $e->getMessage());

                    throw $e;
                }
            });
    }

    /**
     * Proses satu baris data untuk disimpan.
     */
    protected static function importRow(array $row, int $index): void
    {
        if (empty($row['id']) || empty($row['name'])) {
            throw new Exception("Missing required fields (id or name).");
        }

        Province::updateOrCreate(
            ['id' => $row['id']],
            [
                'name' => $row['name'],
                'slug' => Str::slug($row['name']),
                'latitude' => self::parseCoordinate($row['lat'] ?? null),
                'longitude' => self::parseCoordinate($row['lon'] ?? null),
            ]
        );
    }

    /**
     * Parsing koordinat ke float/null.
     */
    protected static function parseCoordinate($value): ?float
    {
        return is_numeric($value) ? (float) $value : null;
    }
}
