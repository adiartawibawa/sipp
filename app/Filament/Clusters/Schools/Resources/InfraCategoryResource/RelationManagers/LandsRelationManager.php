<?php

namespace App\Filament\Clusters\Schools\Resources\InfraCategoryResource\RelationManagers;

use App\Models\Schools\Land;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class LandsRelationManager extends RelationManager
{
    protected static string $relationship = 'lands';

    protected static ?string $title = 'Daftar Tanah';

    protected static ?string $modelLabel = 'Tanah';

    protected static ?string $pluralModelLabel = 'Tanah';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nama Tanah')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('area')
                    ->label('Luas (m²)')
                    ->numeric()
                    ->required(),

                Forms\Components\TextInput::make('latitude')
                    ->label('Latitude')
                    ->numeric()
                    ->required(),

                Forms\Components\TextInput::make('longitude')
                    ->label('Longitude')
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

                Tables\Columns\TextColumn::make('area')
                    ->label('Luas (m²)')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('latitude')
                    ->label('Latitude'),

                Tables\Columns\TextColumn::make('longitude')
                    ->label('Longitude'),
            ])
            ->filters([
                Tables\Filters\Filter::make('has_area')
                    ->label('Hanya yang memiliki luas')
                    ->query(fn(Builder $query) => $query->where('area', '>', 0)),
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
