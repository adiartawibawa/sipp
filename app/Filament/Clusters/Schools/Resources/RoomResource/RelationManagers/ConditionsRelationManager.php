<?php

namespace App\Filament\Clusters\Schools\Resources\RoomResource\RelationManagers;

use App\Models\Schools\InfraCondition;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ConditionsRelationManager extends RelationManager
{
    protected static string $relationship = 'conditions';

    protected static ?string $title = 'Kondisi Ruangan';

    protected static ?string $modelLabel = 'Kondisi';

    protected static ?string $pluralModelLabel = 'Daftar Kondisi';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('condition_type')
                    ->options([
                        'structural' => 'Struktural',
                        'electrical' => 'Kelistrikan',
                        'plumbing' => 'Plumbing',
                        'finishing' => 'Finishing',
                        'other' => 'Lainnya',
                    ])
                    ->required()
                    ->label('Jenis Kondisi'),
                Forms\Components\Select::make('condition_status')
                    ->options([
                        'excellent' => 'Sangat Baik',
                        'good' => 'Baik',
                        'fair' => 'Cukup',
                        'poor' => 'Buruk',
                        'critical' => 'Kritis',
                    ])
                    ->required()
                    ->label('Status Kondisi'),
                Forms\Components\DatePicker::make('assessment_date')
                    ->required()
                    ->label('Tanggal Penilaian'),
                Forms\Components\Textarea::make('description')
                    ->label('Deskripsi')
                    ->maxLength(500)
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('repair_cost_estimate')
                    ->label('Perkiraan Biaya Perbaikan')
                    ->numeric()
                    ->prefix('Rp'),
                Forms\Components\DatePicker::make('repair_schedule')
                    ->label('Jadwal Perbaikan'),
                Forms\Components\Textarea::make('notes')
                    ->label('Catatan')
                    ->maxLength(500)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('condition_type')
            ->columns([
                Tables\Columns\TextColumn::make('condition_type')
                    ->label('Jenis Kondisi')
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'structural' => 'Struktural',
                        'electrical' => 'Kelistrikan',
                        'plumbing' => 'Plumbing',
                        'finishing' => 'Finishing',
                        default => 'Lainnya',
                    })
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'structural' => 'danger',
                        'electrical' => 'warning',
                        'plumbing' => 'info',
                        'finishing' => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('condition_status')
                    ->label('Status')
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'excellent' => 'Sangat Baik',
                        'good' => 'Baik',
                        'fair' => 'Cukup',
                        'poor' => 'Buruk',
                        'critical' => 'Kritis',
                    })
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'excellent' => 'success',
                        'good' => 'primary',
                        'fair' => 'info',
                        'poor' => 'warning',
                        'critical' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('assessment_date')
                    ->label('Tanggal Penilaian')
                    ->date(),
                Tables\Columns\TextColumn::make('repair_cost_estimate')
                    ->label('Perkiraan Biaya')
                    ->numeric(decimalPlaces: 0)
                    ->prefix('Rp ')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('repair_schedule')
                    ->label('Jadwal Perbaikan')
                    ->date()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('condition_type')
                    ->label('Jenis Kondisi')
                    ->options([
                        'structural' => 'Struktural',
                        'electrical' => 'Kelistrikan',
                        'plumbing' => 'Plumbing',
                        'finishing' => 'Finishing',
                        'other' => 'Lainnya',
                    ]),
                Tables\Filters\SelectFilter::make('condition_status')
                    ->label('Status Kondisi')
                    ->options([
                        'excellent' => 'Sangat Baik',
                        'good' => 'Baik',
                        'fair' => 'Cukup',
                        'poor' => 'Buruk',
                        'critical' => 'Kritis',
                    ]),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Tambah Kondisi Baru'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    protected function getTableQuery(): Builder
    {
        return parent::getTableQuery()
            ->orderBy('assessment_date', 'desc')
            ->orderBy('condition_status');
    }
}
