<?php

namespace App\Filament\Clusters\Administrasi\Resources;

use App\Filament\Clusters\Administrasi;
use App\Filament\Clusters\Administrasi\Resources\DistrictResource\Pages;
use App\Filament\Clusters\Administrasi\Resources\DistrictResource\RelationManagers;
use App\Filament\Clusters\Administrasi\Resources\DistrictResource\RelationManagers\VillagesRelationManager;
use App\Models\Regions\District;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class DistrictResource extends Resource
{
    protected static ?string $model = District::class;

    protected static ?string $navigationIcon = 'heroicon-o-folder-open';

    protected static ?string $cluster = Administrasi::class;

    protected static ?string $navigationGroup = 'Regions';

    protected static ?string $modelLabel = 'Kecamatan';

    protected static ?string $pluralModelLabel = 'Daftar Kecamatan';

    protected static ?string $navigationLabel = 'Kecamatan';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Identitas Kecamatan')
                    ->schema([
                        Forms\Components\TextInput::make('id')
                            ->label('Kode Kecamatan')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->helperText('Kode berdasarkan PERMENDAGRI'),

                        Forms\Components\Select::make('regency_id')
                            ->relationship('regency', 'name')
                            ->label('Kabupaten/Kota')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->native(false),

                        Forms\Components\TextInput::make('name')
                            ->label('Nama Kecamatan')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn($state, $set) => $set('slug', Str::slug($state))),

                        Forms\Components\TextInput::make('slug')
                            ->label('Slug')
                            ->maxLength(255)
                            ->disabled()
                            ->dehydrated()
                            ->unique(ignoreRecord: true),
                    ])->columns(2),

                Forms\Components\Section::make('Koordinat Geografis')
                    ->schema([
                        Forms\Components\TextInput::make('latitude')
                            ->label('Lintang')
                            ->numeric()
                            ->step('0.000001')
                            ->nullable(),

                        Forms\Components\TextInput::make('longitude')
                            ->label('Bujur')
                            ->numeric()
                            ->step('0.000001')
                            ->nullable(),
                    ])->columns(2)
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
                    ->label('Nama Kecamatan')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('regency.name')
                    ->label('Kabupaten/Kota')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('villages_count')
                    ->label('Jumlah Desa/Kelurahan')
                    ->counts('villages')
                    ->sortable(),

                Tables\Columns\TextColumn::make('slug')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('regency')
                    ->relationship('regency', 'name')
                    ->label('Filter Kabupaten/Kota')
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
            ->defaultSort('regency.id')
            ->defaultSort('id');
    }

    public static function getRelations(): array
    {
        return [
            VillagesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDistricts::route('/'),
            'create' => Pages\CreateDistrict::route('/create'),
            'edit' => Pages\EditDistrict::route('/{record}/edit'),
        ];
    }
}
