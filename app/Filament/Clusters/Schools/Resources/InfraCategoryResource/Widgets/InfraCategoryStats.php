<?php

namespace App\Filament\Clusters\Schools\Resources\InfraCategoryResource\Widgets;

use App\Models\Schools\InfraCategory;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class InfraCategoryStats extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Kategori', InfraCategory::count())
                ->description('Jumlah total kategori infrastruktur')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            Stat::make('Kategori dengan Tanah', InfraCategory::has('lands')->count())
                ->description('Kategori yang memiliki data tanah')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('info'),

            Stat::make('Kategori dengan Bangunan', InfraCategory::has('buildings')->count())
                ->description('Kategori yang memiliki data bangunan')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('warning'),
        ];
    }
}
