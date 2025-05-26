<?php

namespace App\Filament\Clusters\Administrasi\Resources\RegencyResource\Actions;

use App\Models\Regions\Regency;
use App\Services\CsvProcessor;
use Exception;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImportRegenciesAction extends Action
{
    public static function make(?string $name = null): static
    {
        return parent::make($name)
            ->label('Import CSV')
            ->icon('heroicon-o-arrow-up-tray')
            ->color('success')
            ->modalHeading('Import Regency from CSV')
            ->modalDescription('Upload a CSV file with regency data. Format kolom: id, province_id, name, lat, lon.')
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
                    ->directory('regency-imports')
                    ->preserveFilenames()
                    ->maxSize(1024)
                    ->helperText('Kolom: id, province_id, name, lat, lon')
                    ->rules(['file', 'mimetypes:text/csv,text/plain']),
            ])
            ->action(fn(array $data) => self::handleImport($data['file']));
    }

    protected static function handleImport(string $file): void
    {
        try {
            // Validasi file
            if (!Storage::disk('public')->exists($file)) {
                throw new Exception("File not found in storage.");
            }

            $filePath = Storage::disk('public')->path($file);
            if (!is_readable($filePath)) {
                throw new Exception("File is not readable.");
            }

            // Load data dengan chunk
            $csvProcessor = app(CsvProcessor::class);
            $chunks = $csvProcessor->process($filePath, [
                'header' => ['id', 'province_id', 'name', 'lat', 'lon'],
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
                            $message = "Chunk {$chunkIndex} - Row {$rowIndex}: " . $e->getMessage();
                            $allErrors[] = $message;
                            Log::error('[Regency Import] ' . $message, ['row' => $row]);
                        }
                    }

                    DB::commit();
                } catch (Exception $e) {
                    DB::rollBack();
                    $message = "Chunk {$chunkIndex} failed entirely: " . $e->getMessage();
                    $allErrors[] = $message;
                    Log::error('[Regency Import] ' . $message);
                }
            });

            // Notifikasi
            self::notifyResult($totalImported, $allErrors);
        } catch (Exception $e) {
            Log::error('[Regency Import] Critical Error: ' . $e->getMessage());

            Notification::make()
                ->title('Import Failed')
                ->body("Error: " . $e->getMessage())
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->send();

            throw $e;
        }
    }

    /**
     * Memproses satu baris data tanpa transaksi.
     */
    protected static function importRow(array $row, int $index): void
    {
        if (empty($row['id']) || empty($row['name'])) {
            throw new Exception("Missing required fields (id or name).");
        }

        Regency::updateOrCreate(
            ['id' => $row['id']],
            [
                'province_id' => $row['province_id'],
                'name' => $row['name'],
                'slug' => Str::slug($row['name']),
                'latitude' => self::parseCoordinate($row['lat'] ?? null),
                'longitude' => self::parseCoordinate($row['lon'] ?? null),
            ]
        );
    }

    protected static function parseCoordinate($value): ?float
    {
        return is_numeric($value) ? (float) $value : null;
    }

    protected static function notifyResult(int $totalImported, array $errors): void
    {
        $message = "Successfully imported {$totalImported} regencies.";

        if ($errors) {
            $message .= "\n\n" . count($errors) . " rows failed:\n" .
                implode("\n", array_slice($errors, 0, 5));

            if (count($errors) > 5) {
                $message .= "\n...and " . (count($errors) - 5) . " more";
            }
        }

        Notification::make()
            ->title($totalImported > 0 ? 'Import Completed' : 'Import Failed')
            ->body($message)
            ->icon($totalImported > 0 ? 'heroicon-o-check-circle' : 'heroicon-o-exclamation-circle')
            ->color($totalImported > 0 ? 'success' : 'danger')
            ->send();
    }
}
