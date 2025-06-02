<?php

namespace App\Filament\Clusters\Schools\Resources\InfraConditionResource\Widgets;

use App\Models\Schools\InfraCondition;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentInfraConditions extends BaseWidget
{
    protected static ?string $heading = 'Pemeriksaan Kondisi Terbaru';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 2;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                InfraCondition::query()
                    ->with('entity')
                    ->latest('checked_at')
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('entity.name')
                    ->label('Entitas')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('condition')
                    ->label('Kondisi')
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'good' => 'Baik',
                        'light' => 'Rusak Ringan',
                        'heavy' => 'Rusak Berat',
                        default => $state,
                    })
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'good' => 'success',
                        'light' => 'warning',
                        'heavy' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('percentage')
                    ->label('Persentase')
                    ->numeric(decimalPlaces: 2)
                    ->suffix('%')
                    ->sortable(),

                Tables\Columns\TextColumn::make('checked_at')
                    ->label('Tanggal Pemeriksaan')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('notes')
                    ->label('Catatan')
                    ->limit(30)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        return strlen($state) > 30 ? $state : null;
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->url(fn(InfraCondition $record): string => route('filament.admin.resources.schools.infra-conditions.view', $record)),
            ]);
    }

    // public static function canView(): bool
    // {
    //     return auth()->user()->can('view_any', InfraCondition::class);
    // }
}
