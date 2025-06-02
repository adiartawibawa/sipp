<?php

namespace App\Filament\Clusters\Schools\Resources\InfraConditionResource\Widgets;

use App\Models\Schools\InfraCondition;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class InfraConditionDistribution extends ChartWidget
{
    protected static ?string $heading = 'Distribusi Kondisi per Jenis Entitas';

    protected static ?string $maxHeight = '300px';

    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        $data = InfraCondition::query()
            ->select('entity_type', 'condition', DB::raw('count(*) as total'))
            ->groupBy('entity_type', 'condition')
            ->get()
            ->groupBy('entity_type');

        $labels = ['Gedung', 'Ruangan', 'Fasilitas'];
        $goodData = [];
        $lightData = [];
        $heavyData = [];

        foreach ($labels as $label) {
            $type = strtolower($label);
            $goodData[] = $data->get($type)?->where('condition', 'good')->sum('total') ?? 0;
            $lightData[] = $data->get($type)?->where('condition', 'light')->sum('total') ?? 0;
            $heavyData[] = $data->get($type)?->where('condition', 'heavy')->sum('total') ?? 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Baik',
                    'data' => $goodData,
                    'backgroundColor' => '#10B981',
                ],
                [
                    'label' => 'Rusak Ringan',
                    'data' => $lightData,
                    'backgroundColor' => '#F59E0B',
                ],
                [
                    'label' => 'Rusak Berat',
                    'data' => $heavyData,
                    'backgroundColor' => '#EF4444',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'x' => [
                    'stacked' => true,
                    'title' => [
                        'display' => true,
                        'text' => 'Jenis Entitas',
                    ],
                ],
                'y' => [
                    'stacked' => true,
                    'title' => [
                        'display' => true,
                        'text' => 'Jumlah Kondisi',
                    ],
                    'beginAtZero' => true,
                ],
            ],
            'plugins' => [
                'legend' => [
                    'position' => 'top',
                ],
                'tooltip' => [
                    'callbacks' => [
                        'afterBody' => 'function(context) {
                            const datasetTotals = context[0].dataset.data.reduce((a, b) => a + b, 0);
                            const total = context.reduce((a, b) => a + b.dataset.data[b.dataIndex], 0);
                            const percentage = Math.round((context[0].raw / total) * 100);
                            return `Persentase: ${percentage}% dari total kondisi`;
                        }',
                    ],
                ],
            ],
        ];
    }
}
