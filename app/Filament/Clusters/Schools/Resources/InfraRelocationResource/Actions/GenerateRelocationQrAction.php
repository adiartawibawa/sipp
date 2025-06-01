<?php

namespace App\Filament\Clusters\Schools\Resources\InfraRelocationResource\Actions;

use App\Models\Schools\InfraRelocation;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class GenerateRelocationQrAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'generate_qr';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Generate QR Code');
        $this->icon('heroicon-o-qr-code');
        $this->color('info');
        $this->modalHeading('Generate QR Code Pemindahan');
        $this->modalDescription('Buat QR Code untuk melacak riwayat pemindahan ini.');
        $this->action(function (InfraRelocation $record) {
            // try {
            //     $qrContent = "Pemindahan ID: {$record->id}\n";
            //     $qrContent .= "Entitas: {$record->entity->name}\n";
            //     $qrContent .= "Dari: {$record->from}\n";
            //     $qrContent .= "Ke: {$record->to}\n";
            //     $qrContent .= "Tanggal: {$record->moved_at->format('d/m/Y')}";

            //     $qrCode = QrCode::size(200)->generate($qrContent);

            //     Notification::make()
            //         ->title('QR Code Berhasil Dibuat')
            //         ->body('QR Code untuk pemindahan ini telah siap.')
            //         ->success()
            //         ->send();

            //     return response()->streamDownload(
            //         fn() => print($qrCode),
            //         "relocation-{$record->id}.svg",
            //         ['Content-Type' => 'image/svg+xml']
            //     );
            // } catch (\Exception $e) {
            //     Notification::make()
            //         ->title('Gagal Generate QR Code')
            //         ->body('Terjadi kesalahan: ' . $e->getMessage())
            //         ->danger()
            //         ->send();

            //     throw $e;
            // }
        });
    }
}
