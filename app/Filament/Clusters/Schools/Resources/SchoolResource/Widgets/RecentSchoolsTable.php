<?php

namespace App\Filament\Clusters\Schools\Resources\SchoolResource\Widgets;

use App\Models\Schools\School;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentSchoolsTable extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                School::query()
                    ->with(['province', 'regency'])
                    ->latest()
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Sekolah')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('npsn')
                    ->label('NPSN')
                    ->searchable(),
                Tables\Columns\TextColumn::make('edu_type')
                    ->label('Jenjang')
                    ->badge()
                    ->formatStateUsing(function (?string $state) {
                        $types = collect(School::defaultEduType())->pluck('name', 'code');
                        return $types[$state] ?? $state ?? 'Unknown';
                    })
                    ->color(function (?string $state) {
                        $colors = [
                            'TPA' => 'info',
                            'KB' => 'primary',
                            'TK' => 'success',
                            'SD' => 'warning',
                            'SMP' => 'danger',
                            'SMA' => 'gray',
                            'SMK' => 'info',
                            'SLB' => 'primary',
                            'SKB' => 'success',
                            'PKBM' => 'warning',
                        ];
                        return $colors[$state] ?? 'gray';
                    }),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'negeri' => 'success',
                        'swasta' => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('regency.name')
                    ->label('Kabupaten/Kota')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Ditambahkan')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ]);
    }
}
