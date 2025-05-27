<?php

namespace App\Filament\Clusters\Schools\Resources\OtherFacilityResource\RelationManagers;

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

    protected static ?string $title = 'Riwayat Pemindahan';

    protected static ?string $modelLabel = 'Pemindahan';

    protected static ?string $pluralModelLabel = 'Riwayat Pemindahan';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('date')
                    ->label('Tanggal Pemindahan')
                    ->required()
                    ->default(now()),

                Forms\Components\TextInput::make('from_location')
                    ->label('Dari Lokasi')
                    ->maxLength(255)
                    ->required(),

                Forms\Components\TextInput::make('to_location')
                    ->label('Ke Lokasi')
                    ->maxLength(255)
                    ->required(),

                Forms\Components\TextInput::make('reason')
                    ->label('Alasan')
                    ->maxLength(255)
                    ->required(),

                Forms\Components\Textarea::make('notes')
                    ->label('Catatan')
                    ->maxLength(65535)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
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
                    ->searchable(),

                Tables\Columns\TextColumn::make('notes')
                    ->label('Catatan')
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Tambah Riwayat'),
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
}
