<?php

namespace App\Filament\Clusters\Schools\Resources\InfraConditionResource\Widgets;

use App\Models\Schools\InfraCondition;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class InfraConditionStats extends BaseWidget
{
    protected static ?string $pollingInterval = '30s';

    protected static bool $isLazy = true;

    protected function getStats(): array
    {
        $conditions = InfraCondition::query()
            ->select('condition', DB::raw('count(*) as total'))
            ->groupBy('condition')
            ->get()
            ->keyBy('condition');

        $totalGood = $conditions->get('good')?->total ?? 0;
        $totalLight = $conditions->get('light')?->total ?? 0;
        $totalHeavy = $conditions->get('heavy')?->total ?? 0;
        $totalAll = $totalGood + $totalLight + $totalHeavy;

        return [
            Stat::make('Kondisi Baik', $totalGood)
                ->description($totalAll > 0 ? round(($totalGood / $totalAll) * 100, 2) . '%' : '0%')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success')
                ->chart($this->getConditionTrend('good')),

            Stat::make('Rusak Ringan', $totalLight)
                ->description($totalAll > 0 ? round(($totalLight / $totalAll) * 100, 2) . '%' : '0%')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('warning')
                ->chart($this->getConditionTrend('light')),

            Stat::make('Rusak Berat', $totalHeavy)
                ->description($totalAll > 0 ? round(($totalHeavy / $totalAll) * 100, 2) . '%' : '0%')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger')
                ->chart($this->getConditionTrend('heavy')),
        ];
    }

    protected function getConditionTrend(string $condition): array
    {
        return InfraCondition::query()
            ->select(DB::raw('DATE(checked_at) as date'), DB::raw('count(*) as count'))
            ->where('condition', $condition)
            ->where('checked_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count')
            ->toArray();
    }

    // public static function canView(): bool
    // {
    //     return auth()->user()->can('view_any', InfraCondition::class);
    // }
}
