<?php

namespace App\Filament\Clusters\Schools\Resources\RoomReferenceResource\Widgets;

use App\Models\Schools\RoomReference;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class RoomReferenceStats extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Referensi', RoomReference::count())
                ->description('Jumlah total referensi ruangan')
                ->descriptionIcon('heroicon-m-home-modern')
                ->color('success'),

            Stat::make('Referensi dengan Kode', RoomReference::whereNotNull('code')->count())
                ->description('Referensi yang memiliki kode identifikasi')
                ->descriptionIcon('heroicon-m-qr-code')
                ->color('info'),

            Stat::make('Rata-rata Ruangan', round(RoomReference::withCount('rooms')->get()->avg('rooms_count'), 1))
                ->description('Rata-rata ruangan per referensi')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('warning'),
        ];
    }
}
