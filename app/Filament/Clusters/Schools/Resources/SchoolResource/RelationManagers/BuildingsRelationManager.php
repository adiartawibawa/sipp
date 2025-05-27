<?php

namespace App\Filament\Clusters\Schools\Resources\SchoolResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class BuildingsRelationManager extends RelationManager
{
    protected static string $relationship = 'buildings';

    protected static ?string $title = 'Bangunan Sekolah';

    protected static ?string $modelLabel = 'Bangunan';

    protected static ?string $pluralModelLabel = 'Daftar Bangunan';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nama Bangunan')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (Set $set, ?string $state) {
                        $set('slug', Str::slug($state));
                    }),
                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->disabled()
                    ->dehydrated(),
                Forms\Components\TextInput::make('type')
                    ->label('Tipe Bangunan')
                    ->required(),
                Forms\Components\TextInput::make('area')
                    ->label('Luas (m²)')
                    ->numeric()
                    ->required()
                    ->step(0.01),
                Forms\Components\TextInput::make('construction_year')
                    ->label('Tahun Pembangunan')
                    ->numeric()
                    ->minValue(1900)
                    ->maxValue(now()->year),
                Forms\Components\Select::make('condition')
                    ->label('Kondisi')
                    ->options([
                        'baik' => 'Baik',
                        'rusak_ringan' => 'Rusak Ringan',
                        'rusak_sedang' => 'Rusak Sedang',
                        'rusak_berat' => 'Rusak Berat',
                    ])
                    ->required(),
                Forms\Components\Textarea::make('description')
                    ->label('Deskripsi')
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
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Tipe')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('area')
                    ->label('Luas (m²)')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('condition')
                    ->label('Kondisi')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'baik' => 'success',
                        'rusak_ringan' => 'info',
                        'rusak_sedang' => 'warning',
                        'rusak_berat' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('construction_year')
                    ->label('Tahun Dibangun')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('condition')
                    ->label('Kondisi Bangunan')
                    ->options([
                        'baik' => 'Baik',
                        'rusak_ringan' => 'Rusak Ringan',
                        'rusak_sedang' => 'Rusak Sedang',
                        'rusak_berat' => 'Rusak Berat',
                    ]),
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
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }
}
