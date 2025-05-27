<?php

namespace App\Filament\Clusters\Schools\Resources\RoomResource\RelationManagers;

use App\Models\Schools\InfraAcquisition;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AcquisitionsRelationManager extends RelationManager
{
    protected static string $relationship = 'acquisitions';

    protected static ?string $title = 'Riwayat Perolehan';

    protected static ?string $modelLabel = 'Perolehan';

    protected static ?string $pluralModelLabel = 'Riwayat Perolehan';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('source')
                    ->options([
                        'purchase' => 'Pembelian',
                        'grant' => 'Hibah',
                        'construction' => 'Pembangunan',
                        'transfer' => 'Transfer',
                        'other' => 'Lainnya',
                    ])
                    ->required()
                    ->label('Sumber Perolehan'),
                Forms\Components\TextInput::make('amount')
                    ->label('Nilai Perolehan')
                    ->numeric()
                    ->prefix('Rp'),
                Forms\Components\TextInput::make('year')
                    ->label('Tahun Perolehan')
                    ->numeric()
                    ->minValue(1900)
                    ->maxValue(now()->year),
                Forms\Components\Select::make('method')
                    ->options([
                        'cash' => 'Tunai',
                        'credit' => 'Kredit',
                        'installment' => 'Cicilan',
                        'other' => 'Lainnya',
                    ])
                    ->label('Metode Perolehan'),
                Forms\Components\Textarea::make('notes')
                    ->label('Catatan')
                    ->maxLength(500)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('source')
            ->columns([
                Tables\Columns\TextColumn::make('source')
                    ->label('Sumber')
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'purchase' => 'Pembelian',
                        'grant' => 'Hibah',
                        'construction' => 'Pembangunan',
                        'transfer' => 'Transfer',
                        default => 'Lainnya',
                    })
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'purchase' => 'success',
                        'grant' => 'info',
                        'construction' => 'warning',
                        'transfer' => 'primary',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Nilai')
                    ->numeric(decimalPlaces: 0)
                    ->prefix('Rp '),
                Tables\Columns\TextColumn::make('year')
                    ->label('Tahun')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('method')
                    ->label('Metode')
                    ->formatStateUsing(fn(?string $state): string => match ($state) {
                        'cash' => 'Tunai',
                        'credit' => 'Kredit',
                        'installment' => 'Cicilan',
                        default => 'Lainnya',
                    })
                    ->badge()
                    ->color(fn(?string $state): string => match ($state) {
                        'cash' => 'success',
                        'credit' => 'danger',
                        'installment' => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dicatat')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('source')
                    ->label('Sumber Perolehan')
                    ->options([
                        'purchase' => 'Pembelian',
                        'grant' => 'Hibah',
                        'construction' => 'Pembangunan',
                        'transfer' => 'Transfer',
                        'other' => 'Lainnya',
                    ]),
                Tables\Filters\SelectFilter::make('year')
                    ->label('Tahun Perolehan')
                    ->options(fn() => InfraAcquisition::query()
                        ->distinct('year')
                        ->orderBy('year', 'desc')
                        ->pluck('year', 'year')
                        ->toArray()),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Tambah Riwayat Perolehan'),
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
            ])
            ->defaultSort('year', 'desc');
    }
}
