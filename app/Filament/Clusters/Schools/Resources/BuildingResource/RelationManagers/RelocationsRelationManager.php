<?php

namespace App\Filament\Clusters\Schools\Resources\BuildingResource\RelationManagers;

use App\Models\Schools\InfraRelocation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class RelocationsRelationManager extends RelationManager
{
    protected static string $relationship = 'relocations';

    protected static ?string $modelLabel = 'Pemindahan';

    protected static ?string $pluralModelLabel = 'Riwayat Pemindahan Bangunan';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('date')
                    ->label('Tanggal Pemindahan')
                    ->required(),

                Forms\Components\TextInput::make('from_location')
                    ->label('Dari Lokasi')
                    ->maxLength(100)
                    ->required(),

                Forms\Components\TextInput::make('to_location')
                    ->label('Ke Lokasi')
                    ->maxLength(100)
                    ->required(),

                Forms\Components\TextInput::make('reason')
                    ->label('Alasan Pemindahan')
                    ->maxLength(200)
                    ->required(),

                Forms\Components\TextInput::make('cost')
                    ->label('Biaya Pemindahan')
                    ->numeric()
                    ->prefix('Rp')
                    ->step(1000)
                    ->maxValue(999999999999),

                Forms\Components\Textarea::make('notes')
                    ->label('Catatan')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('date')
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->label('Tanggal')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('from_location')
                    ->label('Dari')
                    ->searchable(),

                Tables\Columns\TextColumn::make('to_location')
                    ->label('Ke')
                    ->searchable(),

                Tables\Columns\TextColumn::make('reason')
                    ->label('Alasan')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('cost')
                    ->label('Biaya')
                    ->numeric(decimalPlaces: 2)
                    ->prefix('Rp')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dicatat Pada')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('date', 'desc');
    }

    protected function getTableQuery(): Builder
    {
        return parent::getTableQuery()
            ->latest('date');
    }
}
