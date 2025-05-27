<?php

namespace App\Filament\Clusters\Schools\Resources\BuildingResource\Widgets;

use App\Filament\Clusters\Schools\Resources\BuildingResource;
use App\Models\Schools\Building;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class RecentBuildings extends BaseWidget
{
    protected static ?string $heading = 'Bangunan Terbaru';
    protected int|string|array $columnSpan = 'full';
    protected static ?string $pollingInterval = null;
    protected static bool $isLazy = true;

    protected function getTableQuery(): Builder
    {
        return Building::query()
            ->with(['school', 'category'])
            ->latest()
            ->limit(5);
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('name')
                ->label('Nama Bangunan')
                ->searchable()
                ->sortable()
                ->description(fn(Building $record) => $record->school->name),

            Tables\Columns\TextColumn::make('category.name')
                ->label('Kategori')
                ->searchable()
                ->sortable(),

            Tables\Columns\TextColumn::make('area')
                ->label('Luas')
                ->numeric(decimalPlaces: 2)
                ->suffix(' m²')
                ->sortable(),

            Tables\Columns\TextColumn::make('build_year')
                ->label('Tahun Dibangun')
                ->numeric()
                ->sortable(),

            Tables\Columns\TextColumn::make('created_at')
                ->label('Ditambahkan')
                ->dateTime()
                ->sortable(),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            Tables\Actions\Action::make('view')
                ->label('Lihat')
                ->url(fn(Building $record): string => BuildingResource::getUrl('edit', ['record' => $record])),
        ];
    }

    protected function isTablePaginationEnabled(): bool
    {
        return false;
    }

    // public static function canView(): bool
    // {
    //     return auth()->user()->can('viewAny', Building::class);
    // }
}
