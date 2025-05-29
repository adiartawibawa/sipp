<?php

namespace App\Filament\Clusters\Schools\Resources\InfraDocumentResource\Widgets;

use App\Models\Schools\InfraDocument;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DocumentStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $total = InfraDocument::count();
        $recent = InfraDocument::where('created_at', '>', now()->subDays(7))->count();

        return [
            Stat::make('Total Dokumen', $total)
                ->description('Seluruh dokumen infrastruktur')
                ->chart($this->getChartData())
                ->color('primary'),

            Stat::make('Dokumen Baru', $recent)
                ->description('7 hari terakhir')
                ->color('success')
                ->chart($this->getRecentChartData()),

            Stat::make('Rata-rata', round($total / max(1, InfraDocument::distinct('entity_id')->count()), 1))
                ->description('Dokumen per entitas')
                ->color('info'),
        ];
    }

    protected function getChartData(): array
    {
        return InfraDocument::selectRaw('COUNT(*) as count, DATE_FORMAT(created_at, "%Y-%m") as month')
            ->groupBy('month')
            ->orderBy('month')
            ->limit(6)
            ->get()
            ->pluck('count')
            ->toArray();
    }

    protected function getRecentChartData(): array
    {
        return InfraDocument::where('created_at', '>', now()->subDays(7))
            ->selectRaw('COUNT(*) as count, DATE(created_at) as day')
            ->groupBy('day')
            ->orderBy('day')
            ->get()
            ->pluck('count')
            ->toArray();
    }
}
