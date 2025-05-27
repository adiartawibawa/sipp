<?php

namespace App\Filament\Clusters\Schools\Resources\SchoolResource\Widgets;

use App\Models\Schools\School;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class SchoolFacilitiesChart extends ChartWidget
{
    protected static ?string $heading = 'Statistik Fasilitas Sekolah';

    protected function getData(): array
    {
        $schoolData = School::withCount(['buildings', 'rooms', 'otherFacilities'])->get();

        $buildings = $schoolData->sum('buildings_count');
        $rooms = $schoolData->sum('rooms_count');
        $facilities = $schoolData->sum('other_facilities_count');

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Fasilitas',
                    'data' => [$buildings, $rooms, $facilities],
                    'backgroundColor' => [
                        'rgba(54, 162, 235, 0.6)',
                        'rgba(75, 192, 192, 0.6)',
                        'rgba(153, 102, 255, 0.6)',
                    ],
                    'borderColor' => [
                        'rgba(54, 162, 235, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                    ],
                    'borderWidth' => 1,
                ],
            ],
            'labels' => ['Gedung', 'Ruang', 'Fasilitas Lain'],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
