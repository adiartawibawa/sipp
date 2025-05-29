<?php

namespace App\Filament\Clusters\Schools\Resources\InfraLegalResource\Widgets;

use App\Models\Schools\InfraLegal;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class InfraLegalStats extends BaseWidget
{
    protected function getStats(): array
    {
        $stats = [
            'shm' => InfraLegal::where('status', 'shm')->count(),
            'shgb' => InfraLegal::where('status', 'shgb')->count(),
            'hpl' => InfraLegal::where('status', 'hpl')->count(),
            'sewa' => InfraLegal::where('status', 'sewa')->count(),
            'pinjam_pakai' => InfraLegal::where('status', 'pinjam_pakai')->count(),
            'lainnya' => InfraLegal::where('status', 'lainnya')->count(),
        ];

        return [
            Stat::make('SHM', $stats['shm'])
                ->description('Sertifikat Hak Milik')
                ->color('success')
                ->chart($this->getChartData('shm')),

            Stat::make('SHGB', $stats['shgb'])
                ->description('Sertifikat Hak Guna Bangunan')
                ->color('primary')
                ->chart($this->getChartData('shgb')),

            Stat::make('HPL', $stats['hpl'])
                ->description('Hak Pengelolaan Lainnya')
                ->color('warning')
                ->chart($this->getChartData('hpl')),

            Stat::make('Total', array_sum($stats))
                ->description('Total Status Hukum')
                ->color('gray')
                ->chart($this->getTotalChartData()),
        ];
    }

    protected function getChartData(string $status): array
    {
        return InfraLegal::where('status', $status)
            ->selectRaw('COUNT(*) as count, DATE_FORMAT(created_at, "%Y-%m") as month')
            ->groupBy('month')
            ->orderBy('month')
            ->limit(6)
            ->get()
            ->pluck('count')
            ->toArray();
    }

    protected function getTotalChartData(): array
    {
        return InfraLegal::selectRaw('COUNT(*) as count, DATE_FORMAT(created_at, "%Y-%m") as month')
            ->groupBy('month')
            ->orderBy('month')
            ->limit(6)
            ->get()
            ->pluck('count')
            ->toArray();
    }
}
