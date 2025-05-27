<?php

namespace App\Filament\Clusters\Schools\Resources\BuildingResource\Widgets;

use App\Models\Schools\Building;
use App\Models\Schools\InfraCondition;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class BuildingCondition extends ChartWidget
{
    protected static ?string $heading = 'Kondisi Bangunan Terkini';

    protected static ?string $pollingInterval = '30s';

    protected static bool $isLazy = true;

    protected function getData(): array
    {
        $conditions = InfraCondition::query()
            ->select('condition_status_id', DB::raw('count(*) as total'))
            ->where('entity_type', Building::class)
            ->groupBy('condition_status_id')
            ->with('status')
            ->orderBy('condition_status_id')
            ->get();

        return [
            'labels' => $conditions->pluck('status.name'),
            'datasets' => [
                [
                    'label' => 'Jumlah Bangunan',
                    'data' => $conditions->pluck('total'),
                    'backgroundColor' => [
                        '#10b981', // Baik - hijau
                        '#f59e0b', // Rusak Ringan - kuning
                        '#ef4444', // Rusak Berat - merah
                    ],
                    'borderColor' => '#ffffff',
                ],
            ],
        ];
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
                'tooltip' => [
                    'callbacks' => [
                        'label' => function ($context) {
                            return $context['raw'] . ' bangunan';
                        }
                    ]
                ]
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
        return 'bar';
    }
}
