<?php

namespace App\Filament\Clusters\Administrasi\Resources\RegencyResource\Actions;

use App\Services\CsvProcessor;
use Exception;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
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
            ->modalDescription('Upload a CSV file with regency data. The file should contain columns: id, province_id, name, lat, lon')
            ->modalSubmitActionLabel('Import')
            ->form([
                \Filament\Forms\Components\FileUpload::make('file')
                    ->label('CSV File')
                    ->required()
                    ->acceptedFileTypes([
                        'text/csv',
                        'text/plain',
                        'application/csv',
                        'text/comma-separated-values',
                        'application/vnd.ms-excel'
                    ])
                    ->directory('regency-imports')
                    ->preserveFilenames()
                    ->maxSize(1024)
                    ->helperText('Format kolom: id, province_id, name, lat, lon')
                    ->rules(['file', 'mimetypes:text/csv,text/plain']),
            ])
            ->action(function (array $data) {
                try {
                    // Get the correct file path using storage disk
                    $filePath = Storage::disk('public')->path($data['file']);

                    // Verify file exists and is readable
                    if (!Storage::disk('public')->exists($data['file'])) {
                        throw new Exception("The uploaded file could not be found.");
                    }

                    if (!is_readable($filePath)) {
                        throw new Exception("The uploaded file is not readable.");
                    }

                    $csvProcessor = app(CsvProcessor::class);

                    // Process CSV with proper headers
                    $rows = $csvProcessor->process($filePath, [
                        'header' => ['id', 'province_id', 'name', 'lat', 'lon'],
                        'skipHeader' => true,
                    ]);

                    $importCount = 0;
                    $errors = [];

                    foreach ($rows as $index => $row) {
                        try {
                            // Validate required fields
                            if (empty($row['id']) || empty($row['name'])) {
                                throw new Exception("Row {$index}: Missing required fields (id or name)");
                            }

                            // Process data
                            \App\Models\Regions\Regency::updateOrCreate(
                                ['id' => $row['id']],
                                [
                                    'province_id' => $row['province_id'],
                                    'name' => $row['name'],
                                    'slug' => Str::slug($row['name']),
                                    'latitude' => self::parseCoordinate($row['lat'] ?? null),
                                    'longitude' => self::parseCoordinate($row['lon'] ?? null),
                                ]
                            );
                            $importCount++;
                        } catch (\Exception $e) {
                            $errors[] = "Row {$index}: " . $e->getMessage();
                            continue;
                        }
                    }

                    // Prepare notification message
                    $message = "Successfully imported {$importCount} regencies";
                    if (count($errors) > 0) {
                        $message .= "\n\n" . count($errors) . " rows failed:\n" . implode("\n", array_slice($errors, 0, 5));
                        if (count($errors) > 5) {
                            $message .= "\n...and " . (count($errors) - 5) . " more";
                        }
                    }

                    Notification::make()
                        ->title($importCount > 0 ? 'Import Completed' : 'Import Failed')
                        ->body($message)
                        ->icon($importCount > 0 ? 'heroicon-o-check-circle' : 'heroicon-o-exclamation-circle')
                        ->color($importCount > 0 ? 'success' : 'danger')
                        ->send();
                } catch (Exception $e) {
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

    protected static function parseCoordinate($value): ?float
    {
        return is_numeric($value) ? (float) $value : null;
    }
}
