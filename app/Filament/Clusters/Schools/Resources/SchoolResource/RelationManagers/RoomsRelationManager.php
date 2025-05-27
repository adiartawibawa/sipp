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

class RoomsRelationManager extends RelationManager
{
    protected static string $relationship = 'rooms';

    protected static ?string $title = 'Ruang Kelas & Fasilitas';

    protected static ?string $modelLabel = 'Ruang';

    protected static ?string $pluralModelLabel = 'Daftar Ruangan';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nama Ruangan')
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
                Forms\Components\Select::make('type')
                    ->label('Jenis Ruangan')
                    ->options([
                        'kelas' => 'Ruang Kelas',
                        'lab' => 'Laboratorium',
                        'perpustakaan' => 'Perpustakaan',
                        'kantor' => 'Ruang Kantor',
                        'aula' => 'Aula',
                        'olahraga' => 'Ruang Olahraga',
                        'kesehatan' => 'Ruang Kesehatan',
                        'ibadah' => 'Ruang Ibadah',
                        'lainnya' => 'Lainnya',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('capacity')
                    ->label('Kapasitas')
                    ->numeric()
                    ->minValue(1),
                Forms\Components\TextInput::make('area')
                    ->label('Luas (m²)')
                    ->numeric()
                    ->step(0.01),
                Forms\Components\Select::make('building_id')
                    ->label('Gedung')
                    ->relationship('building', 'name')
                    ->searchable()
                    ->preload(),
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
                    ->label('Nama Ruangan')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Jenis')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('building.name')
                    ->label('Gedung')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('capacity')
                    ->label('Kapasitas')
                    ->numeric()
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
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Jenis Ruangan')
                    ->options([
                        'kelas' => 'Ruang Kelas',
                        'lab' => 'Laboratorium',
                        'perpustakaan' => 'Perpustakaan',
                        'kantor' => 'Ruang Kantor',
                        'aula' => 'Aula',
                        'olahraga' => 'Ruang Olahraga',
                        'kesehatan' => 'Ruang Kesehatan',
                        'ibadah' => 'Ruang Ibadah',
                        'lainnya' => 'Lainnya',
                    ]),
                Tables\Filters\SelectFilter::make('condition')
                    ->label('Kondisi Ruangan')
                    ->options([
                        'baik' => 'Baik',
                        'rusak_ringan' => 'Rusak Ringan',
                        'rusak_sedang' => 'Rusak Sedang',
                        'rusak_berat' => 'Rusak Berat',
                    ]),
                Tables\Filters\SelectFilter::make('building_id')
                    ->label('Gedung')
                    ->relationship('building', 'name')
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
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }
}
