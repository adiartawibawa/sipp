<?php

namespace App\Filament\Clusters\Administrasi\Resources;

use App\Filament\Clusters\Administrasi;
use App\Filament\Clusters\Administrasi\Resources\RegencyResource\Pages;
use App\Filament\Clusters\Administrasi\Resources\RegencyResource\RelationManagers;
use App\Filament\Clusters\Administrasi\Resources\RegencyResource\RelationManagers\DistrictsRelationManager;
use App\Models\Regions\Regency;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class RegencyResource extends Resource
{
    protected static ?string $model = Regency::class;

    protected static ?string $navigationIcon = 'heroicon-o-folder-open';

    protected static ?string $cluster = Administrasi::class;

    protected static ?string $navigationGroup = 'Regions';

    protected static ?string $modelLabel = 'Kabupaten/Kota';

    protected static ?string $pluralModelLabel = 'Daftar Kabupaten/Kota';

    protected static ?string $navigationLabel = 'Kabupaten/Kota';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('id')
                    ->label('Kode Kabupaten/Kota')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),

                Forms\Components\Select::make('province_id')
                    ->relationship('province', 'name')
                    ->label('Provinsi')
                    ->required()
                    ->searchable()
                    ->preload(),

                Forms\Components\TextInput::make('name')
                    ->label('Nama Kabupaten/Kota')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function ($state, $set) {
                        $set('slug', Str::slug($state));
                    }),

                Forms\Components\TextInput::make('slug')
                    ->label('Slug')
                    ->maxLength(255)
                    ->disabled()
                    ->dehydrated()
                    ->unique(ignoreRecord: true),

                Forms\Components\TextInput::make('latitude')
                    ->label('Latitude')
                    ->numeric()
                    ->nullable(),

                Forms\Components\TextInput::make('longitude')
                    ->label('Longitude')
                    ->numeric()
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('Kode')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Kabupaten/Kota')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('province.name')
                    ->label('Provinsi')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('districts_count')
                    ->label('Jumlah Kecamatan')
                    ->counts('districts'),

                Tables\Columns\TextColumn::make('slug')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

            ])
            ->filters([
                Tables\Filters\SelectFilter::make('province')
                    ->relationship('province', 'name')
                    ->label('Filter by Provinsi')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('province.id')
            ->defaultSort('id');
    }

    public static function getRelations(): array
    {
        return [
            DistrictsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRegencies::route('/'),
            'create' => Pages\CreateRegency::route('/create'),
            'edit' => Pages\EditRegency::route('/{record}/edit'),
        ];
    }
}
