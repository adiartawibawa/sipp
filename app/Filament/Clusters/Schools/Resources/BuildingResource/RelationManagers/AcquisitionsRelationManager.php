<?php

namespace App\Filament\Clusters\Schools\Resources\BuildingResource\RelationManagers;

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

    protected static ?string $modelLabel = 'Perolehan';

    protected static ?string $pluralModelLabel = 'Riwayat Perolehan Bangunan';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('acquisition_type_id')
                    ->label('Jenis Perolehan')
                    ->relationship('type', 'name')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->native(false),

                Forms\Components\DatePicker::make('date')
                    ->label('Tanggal Perolehan')
                    ->required(),

                Forms\Components\TextInput::make('source')
                    ->label('Sumber Perolehan')
                    ->maxLength(100)
                    ->required(),

                Forms\Components\TextInput::make('cost')
                    ->label('Biaya Perolehan')
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
            ->recordTitleAttribute('type.name')
            ->columns([
                Tables\Columns\TextColumn::make('type.name')
                    ->label('Jenis Perolehan')
                    ->searchable(),

                Tables\Columns\TextColumn::make('date')
                    ->label('Tanggal')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('source')
                    ->label('Sumber')
                    ->searchable(),

                Tables\Columns\TextColumn::make('cost')
                    ->label('Biaya')
                    ->numeric(decimalPlaces: 2)
                    ->prefix('Rp')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dicatat Pada')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->relationship('type', 'name')
                    ->label('Filter Jenis Perolehan')
                    ->searchable()
                    ->preload(),
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
            ->with(['type'])
            ->latest('date');
    }
}
