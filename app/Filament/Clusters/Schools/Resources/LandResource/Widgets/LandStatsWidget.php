<?php

namespace App\Filament\Clusters\Schools\Resources\LandResource\Widgets;

use App\Models\Schools\Land;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class LandStatsWidget extends BaseWidget
{
    protected static ?string $pollingInterval = null;
    protected static bool $isLazy = true;

    protected function getStats(): array
    {
        return [
            Stat::make('Total Tanah', Land::count())
                ->description('Seluruh data tanah terdaftar')
                ->descriptionIcon('heroicon-o-map')
                // ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('success'),

            Stat::make('Tanah Bersertifikat', Land::withCertificate()->count())
                ->description('Memiliki sertifikat resmi')
                ->descriptionIcon('heroicon-o-document-check')
                // ->chart([2, 1, 3, 1, 4, 2, 5])
                ->color('info'),

            Stat::make('Rata-rata Luas', number_format(Land::avg('area') ?? 0, 2) . ' m²')
                ->description('Luas tanah rata-rata')
                ->descriptionIcon('heroicon-o-scale')
                // ->chart([100, 150, 120, 200, 180, 250, 220])
                ->color('warning'),

            Stat::make('Tanah Milik Sendiri', Land::where('ownership', 'owned')->count())
                ->description('Status kepemilikan sekolah')
                ->descriptionIcon('heroicon-o-home')
                // ->chart([3, 1, 2, 2, 3, 4, 5])
                ->color('primary'),
        ];
    }

    // public static function canView(): bool
    // {
    //     return auth()->user()->can('view_land_stats');
    // }
}
