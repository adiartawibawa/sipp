<?php

namespace App\Filament\Clusters\Schools\Resources\OtherFacilityResource\Widgets;

use App\Models\Schools\OtherFacility;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class OtherFacilityStats extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Fasilitas', OtherFacility::count())
                ->description('Jumlah semua fasilitas')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('success'),

            Stat::make('Nilai Total', number_format(OtherFacility::sum('value'), 0, ',', '.'))
                ->description('Total nilai semua fasilitas')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('info'),

            Stat::make('Fasilitas Baru', OtherFacility::where('created_at', '>=', now()->subMonth())->count())
                ->description('Ditambahkan bulan ini')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('warning'),
        ];
    }

    // public static function canView(): bool
    // {
    //     return auth()->user()->can('viewAny', OtherFacility::class);
    // }
}
