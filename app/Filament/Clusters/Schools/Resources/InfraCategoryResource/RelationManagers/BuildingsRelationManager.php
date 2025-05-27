<?php

namespace App\Filament\Clusters\Schools\Resources\InfraCategoryResource\RelationManagers;

use App\Models\Schools\Building;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BuildingsRelationManager extends RelationManager
{
    protected static string $relationship = 'buildings';

    protected static ?string $title = 'Daftar Bangunan';

    protected static ?string $modelLabel = 'Bangunan';

    protected static ?string $pluralModelLabel = 'Bangunan';

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
                    ->numeric()
                    ->required(),

                Forms\Components\TextInput::make('building_area')
                    ->label('Luas Bangunan (m²)')
                    ->numeric()
                    ->required(),

                Forms\Components\TextInput::make('land_area')
                    ->label('Luas Tanah (m²)')
                    ->numeric()
                    ->required(),

                Forms\Components\Textarea::make('address')
                    ->label('Alamat')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable(),

                Tables\Columns\TextColumn::make('floor_count')
                    ->label('Lantai')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('building_area')
                    ->label('Luas Bangunan')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('land_area')
                    ->label('Luas Tanah')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('multi_floor')
                    ->label('Bangunan Bertingkat')
                    ->query(fn(Builder $query) => $query->where('floor_count', '>', 1)),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['infra_cat_id'] = $this->getOwnerRecord()->id;
                        return $data;
                    }),
            ])
            ->actions([
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
