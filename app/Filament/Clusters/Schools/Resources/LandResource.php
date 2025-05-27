<?php

namespace App\Filament\Clusters\Schools\Resources;

use App\Filament\Clusters\Schools;
use App\Filament\Clusters\Schools\Resources\LandResource\Pages;
use App\Filament\Clusters\Schools\Resources\LandResource\RelationManagers;
use App\Models\Schools\Land;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class LandResource extends Resource
{
    protected static ?string $model = Land::class;

    protected static ?string $navigationIcon = 'heroicon-o-map';

    protected static ?string $navigationLabel = 'Tanah';

    protected static ?string $modelLabel = 'Tanah';

    protected static ?string $navigationGroup = 'Fasilitas Sekolah';

    protected static ?string $pluralModelLabel = 'Data Tanah';

    protected static ?string $cluster = Schools::class;

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Dasar Tanah')
                    ->description('Isi data dasar kepemilikan tanah')
                    ->schema([
                        Forms\Components\Select::make('school_id')
                            ->label('Sekolah')
                            ->required()
                            ->relationship('school', 'name')
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->columnSpanFull(),

                        Forms\Components\Select::make('infra_cat_id')
                            ->label('Kategori Infrastruktur')
                            ->required()
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload()
                            ->native(false),

                        Forms\Components\TextInput::make('name')
                            ->label('Nama Tanah')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $operation, $state, Forms\Set $set) {
                                if ($operation !== 'create') {
                                    return;
                                }
                                $set('slug', Str::slug($state));
                            }),

                        Forms\Components\TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->disabled()
                            ->dehydrated(),

                        Forms\Components\TextInput::make('cert_no')
                            ->label('Nomor Sertifikat')
                            ->maxLength(255),

                        // Forms\Components\TextInput::make('latitude')
                        //     ->numeric()
                        //     ->step(0.000001),
                        // Forms\Components\TextInput::make('longitude')
                        //     ->numeric()
                        //     ->step(0.000001),

                        // Forms\Components\FileUpload::make('certificate_file')
                        //     ->label('File Sertifikat')
                        //     ->directory('land-certificates')
                        //     ->acceptedFileTypes(['application/pdf'])
                        //     ->maxSize(2048),

                    ])
                    ->columns(2),

                Forms\Components\Section::make('Detail Ukuran Tanah')
                    ->schema([
                        Forms\Components\TextInput::make('length')
                            ->label('Panjang (m)')
                            ->numeric()
                            ->step(0.01)
                            ->suffix(' meter'),

                        Forms\Components\TextInput::make('width')
                            ->label('Lebar (m)')
                            ->numeric()
                            ->step(0.01)
                            ->suffix(' meter'),

                        Forms\Components\TextInput::make('area')
                            ->label('Luas (m²)')
                            ->numeric()
                            ->step(0.01)
                            ->suffix(' m²')
                            ->readOnly()
                            ->dehydrated(),

                        Forms\Components\TextInput::make('avail_area')
                            ->label('Luas Tersedia (m²)')
                            ->numeric()
                            ->step(0.01)
                            ->suffix(' m²'),

                        Forms\Components\TextInput::make('available_percentage')
                            ->label('Persentase Tersedia')
                            ->numeric()
                            ->step(0.01)
                            ->suffix('%')
                            ->readOnly()
                            ->dehydrated(),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Informasi Kepemilikan')
                    ->schema([
                        Forms\Components\Select::make('ownership')
                            ->label('Status Kepemilikan')
                            ->options([
                                'owned' => 'Milik Sendiri',
                                'leased' => 'Sewa',
                                'borrowed' => 'Pinjam',
                                'other' => 'Lainnya',
                            ])
                            ->native(false),

                        Forms\Components\TextInput::make('njop')
                            ->label('NJOP')
                            ->numeric()
                            ->step(0.01)
                            ->prefix('Rp'),

                        Forms\Components\Textarea::make('notes')
                            ->label('Catatan')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Tanah')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('school.name')
                    ->label('Sekolah')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('category.name')
                    ->label('Kategori')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('area')
                    ->label('Luas (m²)')
                    ->numeric(decimalPlaces: 2)
                    ->sortable(),

                Tables\Columns\TextColumn::make('avail_area')
                    ->label('Tersedia (m²)')
                    ->numeric(decimalPlaces: 2)
                    ->sortable(),

                Tables\Columns\TextColumn::make('available_percentage')
                    ->label('Persentase')
                    ->numeric(decimalPlaces: 2)
                    ->suffix('%')
                    ->sortable(),

                Tables\Columns\TextColumn::make('ownership')
                    ->label('Kepemilikan')
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'owned' => 'Milik Sendiri',
                        'leased' => 'Sewa',
                        'borrowed' => 'Pinjam',
                        'other' => 'Lainnya',
                        default => $state,
                    })
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'owned' => 'success',
                        'leased' => 'warning',
                        'borrowed' => 'info',
                        'other' => 'gray',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('buildings_count')
                    ->label('Bangunan')
                    ->counts('buildings')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('school')
                    ->relationship('school', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Sekolah'),

                Tables\Filters\SelectFilter::make('category')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Kategori Infrastruktur'),

                Tables\Filters\SelectFilter::make('ownership')
                    ->options([
                        'owned' => 'Milik Sendiri',
                        'leased' => 'Sewa',
                        'borrowed' => 'Pinjam',
                        'other' => 'Lainnya',
                    ])
                    ->label('Status Kepemilikan'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
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

    public static function getRelations(): array
    {
        return [
            RelationManagers\BuildingsRelationManager::class,
            RelationManagers\ConditionsRelationManager::class,
            RelationManagers\LegalStatusesRelationManager::class,
            RelationManagers\DocumentsRelationManager::class,
            RelationManagers\AcquisitionsRelationManager::class,
            RelationManagers\RelocationsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLands::route('/'),
            'create' => Pages\CreateLand::route('/create'),
            'edit' => Pages\EditLand::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['school', 'category'])
            ->withCount('buildings');
    }
}
