<?php

namespace App\Filament\Clusters\Schools\Resources\InfraConditionResource\Widgets;

use App\Models\Schools\InfraCondition;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class InfraConditionTrend extends ChartWidget
{
    protected static ?string $heading = 'Tren Kondisi Infrastruktur (30 Hari Terakhir)';

    protected static ?string $pollingInterval = '60s';

    protected static ?string $maxHeight = '300px';

    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        $goodData = Trend::query(InfraCondition::good())
            ->between(
                start: now()->subDays(30),
                end: now(),
            )
            ->perDay()
            ->count();

        $lightData = Trend::query(InfraCondition::lightDamage())
            ->between(
                start: now()->subDays(30),
                end: now(),
            )
            ->perDay()
            ->count();

        $heavyData = Trend::query(InfraCondition::heavyDamage())
            ->between(
                start: now()->subDays(30),
                end: now(),
            )
            ->perDay()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Baik',
                    'data' => $goodData->map(fn(TrendValue $value) => $value->aggregate),
                    'backgroundColor' => '#10B981',
                    'borderColor' => '#10B981',
                    'fill' => false,
                ],
                [
                    'label' => 'Rusak Ringan',
                    'data' => $lightData->map(fn(TrendValue $value) => $value->aggregate),
                    'backgroundColor' => '#F59E0B',
                    'borderColor' => '#F59E0B',
                    'fill' => false,
                ],
                [
                    'label' => 'Rusak Berat',
                    'data' => $heavyData->map(fn(TrendValue $value) => $value->aggregate),
                    'backgroundColor' => '#EF4444',
                    'borderColor' => '#EF4444',
                    'fill' => false,
                ],
            ],
            'labels' => $goodData->map(fn(TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'title' => [
                        'display' => true,
                        'text' => 'Jumlah Kondisi',
                    ],
                ],
                'x' => [
                    'title' => [
                        'display' => true,
                        'text' => 'Tanggal Pemeriksaan',
                    ],
                ],
            ],
            'interaction' => [
                'intersect' => false,
                'mode' => 'index',
            ],
            'plugins' => [
                'tooltip' => [
                    'callbacks' => [
                        'label' => 'function(context) {
                            return context.dataset.label + ": " + context.parsed.y;
                        }',
                    ],
                ],
            ],
        ];
    }
}
