<?php

namespace App\Filament\Clusters\Schools\Resources\FacilityConditionResource\Widgets;

use App\Models\Schools\FacilityCondition;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class FacilityConditionStats extends BaseWidget
{
    protected function getStats(): array
    {
        $total = FacilityCondition::count();
        $good = FacilityCondition::good()->count();
        $light = FacilityCondition::lightDamage()->count();
        $heavy = FacilityCondition::heavyDamage()->count();

        $avgPercentage = FacilityCondition::average('percentage');

        return [
            Stat::make('Total Kondisi', $total)
                ->description('Total kondisi fasilitas')
                ->descriptionIcon('heroicon-m-clipboard-document-list')
                ->color('primary'),

            Stat::make('Rata-rata Kondisi', round($avgPercentage, 1) . '%')
                ->description('Rata-rata persentase kondisi')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('info'),

            Stat::make('Kondisi Baik', $good)
                ->description(round($total > 0 ? ($good / $total * 100) : 0, 1) . '% dari total')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Rusak Ringan', $light)
                ->description(round($total > 0 ? ($light / $total * 100) : 0, 1) . '% dari total')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('warning'),

            Stat::make('Rusak Berat', $heavy)
                ->description(round($total > 0 ? ($heavy / $total * 100) : 0, 1) . '% dari total')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger'),
        ];
    }
}
