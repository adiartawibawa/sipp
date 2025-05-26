<?php

namespace App\Filament\Clusters\Administrasi\Resources;

use App\Filament\Clusters\Administrasi;
use App\Filament\Clusters\Administrasi\Resources\VillageResource\Pages;
use App\Filament\Clusters\Administrasi\Resources\VillageResource\RelationManagers;
use App\Models\Regions\Village;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class VillageResource extends Resource
{
    protected static ?string $model = Village::class;

    protected static ?string $navigationIcon = 'heroicon-o-folder-open';

    protected static ?string $cluster = Administrasi::class;

    protected static ?string $navigationGroup = 'Regions';

    protected static ?string $modelLabel = 'Desa/Kelurahan';

    protected static ?string $pluralModelLabel = 'Daftar Desa/Kelurahan';

    protected static ?string $navigationLabel = 'Desa/Kelurahan';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Identitas Wilayah')
                    ->schema([
                        Forms\Components\TextInput::make('id')
                            ->label('Kode Desa/Kelurahan')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->helperText('Kode berdasarkan PERMENDAGRI'),

                        Forms\Components\Select::make('district_id')
                            ->relationship('district', 'name')
                            ->label('Kecamatan')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->native(false),

                        Forms\Components\TextInput::make('name')
                            ->label('Nama Desa/Kelurahan')
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

                        Forms\Components\TextInput::make('postal_code')
                            ->label('Kode Pos')
                            ->numeric()
                            ->nullable(),
                    ])->columns(2),

                Forms\Components\Section::make('Koordinat dan Metadata')
                    ->schema([
                        Forms\Components\TextInput::make('latitude')
                            ->label('Latitude')
                            ->numeric()
                            ->step('0.000001')
                            ->nullable(),

                        Forms\Components\TextInput::make('longitude')
                            ->label('Longitude')
                            ->numeric()
                            ->step('0.000001')
                            ->nullable(),

                        Forms\Components\KeyValue::make('meta')
                            ->label('Metadata Tambahan')
                            ->keyLabel('Kategori')
                            ->valueLabel('Nilai')
                            ->columnSpanFull(),
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
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Desa/Kelurahan')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('district.name')
                    ->label('Kecamatan')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('postal_code')
                    ->label('Kode Pos')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('slug')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('district')
                    ->relationship('district', 'name')
                    ->label('Filter by Kecamatan')
                    ->searchable()
                    ->preload(),

                Tables\Filters\Filter::make('has_coordinates')
                    ->label('Memiliki Koordinat')
                    ->query(fn($query) => $query->whereNotNull('latitude')->whereNotNull('longitude')),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('district.id')
            ->defaultSort('id');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVillages::route('/'),
            'create' => Pages\CreateVillage::route('/create'),
            'edit' => Pages\EditVillage::route('/{record}/edit'),
        ];
    }
}
