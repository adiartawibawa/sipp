<?php

namespace App\Filament\Clusters\Schools\Resources\LandResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BuildingsRelationManager extends RelationManager
{
    protected static string $relationship = 'buildings';

    protected static ?string $title = 'Bangunan';

    protected static ?string $modelLabel = 'Bangunan';

    protected static ?string $pluralModelLabel = 'Daftar Bangunan';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nama Bangunan')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('floor_count')
                    ->label('Jumlah Lantai')
                    ->required()
                    ->numeric()
                    ->default(1),

                Forms\Components\TextInput::make('room_count')
                    ->label('Jumlah Ruangan')
                    ->numeric()
                    ->default(0),

                Forms\Components\Textarea::make('purpose')
                    ->label('Kegunaan')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Bangunan')
                    ->searchable(),

                Tables\Columns\TextColumn::make('floor_count')
                    ->label('Lantai')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('room_count')
                    ->label('Ruang')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('conditions_count')
                    ->label('Kondisi')
                    ->counts('conditions')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
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
}
