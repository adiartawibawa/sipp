<?php

namespace App\Filament\Clusters\Schools\Resources\InfraConditionResource\Widgets;

use App\Models\Schools\InfraCondition;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class InfraConditionStats extends BaseWidget
{
    protected function getStats(): array
    {
        $total = InfraCondition::count();
        $good = InfraCondition::good()->count();
        $light = InfraCondition::lightDamage()->count();
        $heavy = InfraCondition::heavyDamage()->count();

        $avgPercentage = InfraCondition::average('percentage');

        return [
            Stat::make('Total Kondisi', $total)
                ->description('Total kondisi infrastruktur')
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
