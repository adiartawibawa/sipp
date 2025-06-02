<?php

namespace App\Filament\Clusters\Schools\Resources\SchoolResource\Pages;

use App\Exports\SchoolsTemplateExport;
use App\Filament\Clusters\Schools\Resources\SchoolResource;
use App\Filament\Clusters\Schools\Resources\SchoolResource\Widgets\RecentSchoolsTable;
use App\Filament\Clusters\Schools\Resources\SchoolResource\Widgets\SchoolFacilitiesChart;
use App\Filament\Clusters\Schools\Resources\SchoolResource\Widgets\SchoolStatsOverview;
use App\Imports\SchoolsImport;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ListSchools extends ListRecords
{
    protected static string $resource = SchoolResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),

            Action::make('downloadTemplate')
                ->label('Unduh Template')
                ->icon('heroicon-o-document-text')
                ->outlined()
                ->color('info')
                ->action(function () {
                    return Excel::download(new SchoolsTemplateExport(), 'template-import-sekolah.xlsx');
                }),

            Action::make('import')
                ->label('Import Data')
                ->icon('heroicon-o-arrow-up-tray')
                ->outlined()
                ->color('danger')
                ->form([
                    Section::make('Import Data Sekolah')
                        ->description('Unggah file Excel sesuai template yang disediakan')
                        ->schema([
                            FileUpload::make('file')
                                ->label('File Excel')
                                ->required()
                                ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'])
                                ->preserveFilenames()
                                ->directory('imports/schools')
                                ->visibility('private')
                        ])
                ])
                ->action(function (array $data) {
                    try {

                        $filePath = $data['file'];

                        if (!Storage::disk('public')->exists($filePath)) {
                            throw new \Exception("File $filePath tidak ditemukan.");
                        }

                        Excel::import(new SchoolsImport(), Storage::disk('public')->path($filePath));

                        Notification::make()
                            ->title('Import Berhasil')
                            ->success()
                            ->body('Data sekolah berhasil diimport')
                            ->send();
                    } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
                        $errors = collect($e->failures())->map(function ($failure) {
                            return "Baris {$failure->row()}: " . implode(', ', $failure->errors());
                        })->implode("\n");

                        Notification::make()
                            ->title('Validasi Gagal')
                            ->danger()
                            ->body($errors)
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Import Gagal')
                            ->danger()
                            ->body($e->getMessage())
                            ->send();
                    }
                })
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            SchoolStatsOverview::class,
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            SchoolFacilitiesChart::class,
            RecentSchoolsTable::class,
        ];
    }
}
