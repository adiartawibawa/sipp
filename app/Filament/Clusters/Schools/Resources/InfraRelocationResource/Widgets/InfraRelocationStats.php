<?php

namespace App\Filament\Clusters\Schools\Resources\InfraRelocationResource\Widgets;

use App\Models\Schools\InfraRelocation;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class InfraRelocationStats extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Pemindahan', InfraRelocation::count())
                ->description('Jumlah total pemindahan infrastruktur')
                ->descriptionIcon('heroicon-o-truck')
                ->color('primary'),

            Stat::make('Pemindahan Bulan Ini', InfraRelocation::whereMonth('moved_at', now()->month)->count())
                ->description('Pemindahan yang dilakukan bulan ini')
                ->descriptionIcon('heroicon-o-calendar')
                ->color('success'),

            Stat::make('Paling Sering Dipindah', $this->getMostRelocatedEntity())
                ->description('Entitas dengan pemindahan terbanyak')
                ->descriptionIcon('heroicon-o-arrow-path-rounded-square')
                ->color('warning'),
        ];
    }

    protected function getMostRelocatedEntity(): string
    {
        $entity = InfraRelocation::query()
            ->with('entity')
            ->select('entity_id', 'entity_type')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('entity_id', 'entity_type')
            ->orderByDesc('count')
            ->first();

        return $entity ? $entity->entity->name . ' (' . $entity->count . 'x)' : '-';
    }

    // public static function canView(): bool
    // {
    //     return auth()->user()->can('view_infra_relocation_stats');
    // }
}
