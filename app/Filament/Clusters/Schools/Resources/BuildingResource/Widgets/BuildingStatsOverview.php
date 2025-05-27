<?php

namespace App\Filament\Clusters\Schools\Resources\BuildingResource\Widgets;

use App\Models\Schools\Building;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class BuildingStatsOverview extends BaseWidget
{
    protected static ?string $pollingInterval = null;
    protected static bool $isLazy = true;

    protected function getStats(): array
    {
        return [
            Stat::make('Total Bangunan', Building::count())
                ->description('Jumlah seluruh bangunan')
                ->descriptionIcon('heroicon-o-building-office')
                ->color('primary')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                    'wire:click' => "dispatch('toggleTableFilter', { filter: 'school', value: null })",
                ]),

            Stat::make('Rata-rata Luas', number_format(Building::avg('area') ?? 0, 2) . ' m²')
                ->description('Luas rata-rata bangunan')
                ->descriptionIcon('heroicon-o-arrows-pointing-out')
                ->color('info'),

            Stat::make('Total Nilai Aset', 'Rp ' . number_format(Building::sum('asset_value') ?? 0, 2))
                ->description('Total nilai seluruh bangunan')
                ->descriptionIcon('heroicon-o-banknotes')
                ->color('success'),

            Stat::make('Bangunan Terbaru', Building::latest()->first()?->name ?? '-')
                ->description('Ditambahkan pada ' . (Building::latest()->first()?->created_at?->format('d M Y') ?? '-'))
                ->descriptionIcon('heroicon-o-clock')
                ->color('warning')
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                    'wire:click' => "dispatch('viewRecord', { id: " . Building::latest()->first()?->id . " })",
                ]),
        ];
    }

    // public static function canView(): bool
    // {
    //     return auth()->user()->can('viewAny', Building::class);
    // }
}
