<?php

namespace App\Filament\Clusters\Schools\Resources\BuildingResource\Widgets;

use App\Models\Schools\Building;
use App\Models\Schools\InfraCategory;
use Filament\Widgets\ChartWidget;

class BuildingTypeChart extends ChartWidget
{
    protected static ?string $heading = 'Distribusi Bangunan per Kategori';
    protected static ?string $pollingInterval = null;
    protected static bool $isLazy = true;

    protected function getData(): array
    {
        $categories = InfraCategory::withCount('buildings')->get();

        return [
            'labels' => $categories->pluck('name'),
            'datasets' => [
                [
                    'label' => 'Jumlah Bangunan',
                    'data' => $categories->pluck('buildings_count'),
                    'backgroundColor' => [
                        '#3b82f6',
                        '#10b981',
                        '#f59e0b',
                        '#ef4444',
                        '#8b5cf6',
                        '#ec4899',
                        '#14b8a6',
                        '#f97316',
                        '#64748b',
                        '#06b6d4'
                    ],
                    'borderColor' => '#ffffff',
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'position' => 'right',
                ],
                'tooltip' => [
                    'callbacks' => [
                        'label' => function ($context) {
                            $label = $context['label'];
                            $value = $context['raw'];
                            $total = $context['dataset']['data'][0] + $context['dataset']['data'][1];
                            $percentage = round(($value / $total) * 100, 2);
                            return "$label: $value ($percentage%)";
                        }
                    ]
                ]
            ],
            'responsive' => true,
            'maintainAspectRatio' => false,
        ];
    }

    // public static function canView(): bool
    // {
    //     return auth()->user()->can('viewAny', Building::class);
    // }
}
