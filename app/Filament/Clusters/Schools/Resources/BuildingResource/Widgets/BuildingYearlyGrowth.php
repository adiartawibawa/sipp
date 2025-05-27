<?php

namespace App\Filament\Clusters\Schools\Resources\BuildingResource\Widgets;

use App\Models\Schools\Building;
use Filament\Widgets\ChartWidget;

class BuildingYearlyGrowth extends ChartWidget
{
    protected static ?string $heading = 'Pertumbuhan Bangunan per Tahun';
    protected static ?string $pollingInterval = null;
    protected static bool $isLazy = true;

    protected function getData(): array
    {
        $data = Trend::model(Building::class)
            ->between(
                start: now()->subYears(5),
                end: now(),
            )
            ->perYear()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Bangunan Dibangun',
                    'data' => $data->map(fn(TrendValue $value) => $value->aggregate),
                    'fill' => true,
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'tension' => 0.4,
                ],
            ],
            'labels' => $data->map(fn(TrendValue $value) => $value->date),
        ];
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'precision' => 0,
                    ],
                ],
            ],
            'responsive' => true,
            'maintainAspectRatio' => false,
        ];
    }

    // public static function canView(): bool
    // {
    //     return auth()->user()->can('viewAny', Building::class);
    // }

    protected function getType(): string
    {
        return 'line';
    }
}
