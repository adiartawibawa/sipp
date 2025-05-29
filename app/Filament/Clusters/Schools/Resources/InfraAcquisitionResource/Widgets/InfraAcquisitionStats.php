<?php

namespace App\Filament\Clusters\Schools\Resources\InfraAcquisitionResource\Widgets;

use App\Models\Schools\InfraAcquisition;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class InfraAcquisitionStats extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Perolehan', InfraAcquisition::count())
                ->description('Jumlah total perolehan infrastruktur')
                ->descriptionIcon('heroicon-o-building-library')
                ->color('primary'),

            Stat::make('Nilai Total', number_format(InfraAcquisition::sum('amount'), 2, ',', '.') . ' IDR')
                ->description('Total nilai perolehan')
                ->descriptionIcon('heroicon-o-currency-dollar')
                ->color('success'),

            Stat::make('Rata-rata per Tahun', number_format(InfraAcquisition::average('amount') ?? 0, 2, ',', '.') . ' IDR')
                ->description('Rata-rata nilai perolehan per tahun')
                ->descriptionIcon('heroicon-o-calendar')
                ->color('info'),
        ];
    }

    // public static function canView(): bool
    // {
    //     return auth()->user()->can('view_infra_acquisition_stats');
    // }
}
