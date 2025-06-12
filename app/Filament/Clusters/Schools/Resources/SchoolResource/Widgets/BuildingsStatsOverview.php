<?php

namespace App\Filament\Clusters\Schools\Resources\SchoolResource\Widgets;

use App\Filament\Clusters\Schools\Resources\SchoolResource\Pages\ListSchools;
use App\Models\Schools\Building;
use App\Models\Schools\InfraCondition;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Model;

class BuildingsStatsOverview extends BaseWidget
{
    public ?Model $record = null;

    protected function getStats(): array
    {
        // Jika tidak ada record (misal di halaman index)
        if (!$this->record) {
            return [
                Stat::make('No School Selected', 'Please select a school to view building stats')
                    ->color('gray')
            ];
        }

        // Hitung statistik bangunan
        $buildings = $this->record->buildings;
        $totalBuildings = $buildings->count();
        $totalArea = $buildings->sum('area');

        // Hitung kondisi bangunan
        $conditions = InfraCondition::where('entity_type', Building::class)
            ->whereIn('entity_id', $buildings->pluck('id'))
            ->selectRaw('`condition`, count(*) as count')
            ->groupBy('condition')
            ->pluck('count', 'condition')
            ->toArray();

        return [
            Stat::make('Total Buildings', $totalBuildings)
                ->description('Buildings in this school')
                ->icon('heroicon-o-building-office'),

            Stat::make('Total Area', number_format($totalArea, 2) . ' m²')
                ->description('Total building area')
                ->icon('heroicon-o-scale'),

            Stat::make('Good Condition', $conditions['baik'] ?? 0)
                ->description($totalBuildings ? number_format(($conditions['baik'] ?? 0) / $totalBuildings * 100, 1) . '%' : '0%')
                ->color('success')
                ->icon('heroicon-o-check-circle'),

            Stat::make('Light Damage', $conditions['rusak_ringan'] ?? 0)
                ->description($totalBuildings ? number_format(($conditions['rusak_ringan'] ?? 0) / $totalBuildings * 100, 1) . '%' : '0%')
                ->color('warning')
                ->icon('heroicon-o-exclamation-triangle'),

            Stat::make('Heavy Damage', $conditions['rusak_berat'] ?? 0)
                ->description($totalBuildings ? number_format(($conditions['rusak_berat'] ?? 0) / $totalBuildings * 100, 1) . '%' : '0%')
                ->color('danger')
                ->icon('heroicon-o-x-circle'),
        ];
    }
}
