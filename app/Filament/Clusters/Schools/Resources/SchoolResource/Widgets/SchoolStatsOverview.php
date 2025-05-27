<?php

namespace App\Filament\Clusters\Schools\Resources\SchoolResource\Widgets;

use App\Models\Schools\School;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SchoolStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Sekolah', School::count())
                ->description('Jumlah seluruh sekolah')
                ->descriptionIcon('heroicon-o-building-library')
                ->color('primary'),

            Stat::make('Sekolah Negeri', School::public()->count())
                ->description('Sekolah berstatus negeri')
                ->descriptionIcon('heroicon-o-shield-check')
                ->color('success'),

            Stat::make('Sekolah Swasta', School::private()->count())
                ->description('Sekolah berstatus swasta')
                ->descriptionIcon('heroicon-o-building-storefront')
                ->color('warning'),

            Stat::make('Rata-rata Luas Tanah', number_format(School::with('lands')->get()->avg('total_land_area'), 2) . ' m²')
                ->description('Rata-rata luas tanah per sekolah')
                ->descriptionIcon('heroicon-o-map')
                ->color('info'),
        ];
    }
}
