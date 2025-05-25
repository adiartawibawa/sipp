<?php

namespace App\Filament\Clusters\Administrasi\Resources;

use App\Filament\Clusters\Administrasi;
use App\Filament\Clusters\Administrasi\Resources\ProvinceResource\Pages;
use App\Filament\Clusters\Administrasi\Resources\ProvinceResource\RelationManagers;
use App\Filament\Clusters\Administrasi\Resources\ProvinceResource\RelationManagers\RegenciesRelationManager;
use App\Models\Regions\Province;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class ProvinceResource extends Resource
{
    protected static ?string $model = Province::class;

    protected static ?string $navigationIcon = 'heroicon-o-folder-open';

    protected static ?string $cluster = Administrasi::class;

    protected static ?string $navigationGroup = 'Regions';

    protected static ?string $modelLabel = 'Provinsi';

    protected static ?string $pluralModelLabel = 'Daftar Provinsi';

    protected static ?string $navigationLabel = 'Provinsi';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Provinsi')
                    ->schema([
                        Forms\Components\TextInput::make('id')
                            ->label('Kode Provinsi')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->helperText('Kode provinsi berdasarkan PERMENDAGRI 58/2021'),

                        Forms\Components\TextInput::make('name')
                            ->label('Nama Provinsi')
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
                            ->label('Latitude')
                            ->numeric()
                            ->nullable(),

                        Forms\Components\TextInput::make('longitude')
                            ->label('Longitude')
                            ->numeric()
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
                    ->label('Nama Provinsi')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('regencies_count')
                    ->label('Jumlah Kab/Kota')
                    ->counts('regencies')
                    ->sortable(),

                Tables\Columns\TextColumn::make('slug')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])->defaultSort('name');
    }

    public static function getRelations(): array
    {
        return [
            RegenciesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProvinces::route('/'),
            'create' => Pages\CreateProvince::route('/create'),
            'edit' => Pages\EditProvince::route('/{record}/edit'),
        ];
    }
}
