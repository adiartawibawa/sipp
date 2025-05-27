<?php

namespace App\Filament\Clusters\Schools\Resources\RoomResource\Widgets;

use App\Models\Schools\Room;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class RoomStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Ruangan', Room::count())
                ->description('Jumlah seluruh ruangan')
                ->descriptionIcon('heroicon-o-home-modern')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('success'),
            Stat::make('Rata-rata Luas', number_format(Room::avg('area') ?? 0, 2) . ' m²')
                ->description('Rata-rata luas ruangan')
                ->descriptionIcon('heroicon-o-scale')
                ->color('info'),
            Stat::make('Rata-rata Kapasitas', number_format(Room::avg('capacity') ?? 0, 0))
                ->description('Rata-rata kapasitas ruangan')
                ->descriptionIcon('heroicon-o-users')
                ->color('warning'),
            Stat::make('Total Kapasitas', Room::sum('capacity'))
                ->description('Total kapasitas seluruh ruangan')
                ->descriptionIcon('heroicon-o-user-group')
                ->color('primary'),
        ];
    }

    // public static function canView(): bool
    // {
    //     return auth()->user()->can('viewAny', Room::class);
    // }
}
