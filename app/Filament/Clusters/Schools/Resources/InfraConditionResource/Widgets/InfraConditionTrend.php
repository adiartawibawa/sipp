<?php

namespace App\Filament\Clusters\Schools\Resources\InfraConditionResource\Widgets;

use App\Models\Schools\InfraCondition;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class InfraConditionTrend extends ChartWidget
{
    protected static ?string $heading = 'Trend Kondisi Infrastruktur (6 Bulan Terakhir)';
    protected static ?string $pollingInterval = null;
    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $goodData = Trend::model(InfraCondition::class)
            ->between(start: now()->subMonths(6), end: now())
            ->perMonth()
            // ->good()
            ->count();

        $lightData = Trend::model(InfraCondition::class)
            ->between(start: now()->subMonths(6), end: now())
            ->perMonth()
            // ->lightDamage()
            ->count();

        $heavyData = Trend::model(InfraCondition::class)
            ->between(start: now()->subMonths(6), end: now())
            ->perMonth()
            // ->heavyDamage()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Kondisi Baik',
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
}
