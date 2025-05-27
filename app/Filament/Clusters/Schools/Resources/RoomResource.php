<?php

namespace App\Filament\Clusters\Schools\Resources;

use App\Filament\Clusters\Schools;
use App\Filament\Clusters\Schools\Resources\RoomResource\Pages;
use App\Filament\Clusters\Schools\Resources\RoomResource\RelationManagers;
use App\Models\Schools\Room;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class RoomResource extends Resource
{
    protected static ?string $model = Room::class;

    protected static ?string $navigationIcon = 'heroicon-o-home-modern';

    protected static ?string $cluster = Schools::class;

    protected static ?string $modelLabel = 'Ruangan';

    protected static ?string $pluralModelLabel = 'Data Ruangan';

    protected static ?string $navigationLabel = 'Manajemen Ruangan';

    protected static ?string $navigationGroup = 'Fasilitas Sekolah';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Dasar Ruangan')
                    ->schema([
                        Forms\Components\Select::make('school_id')
                            ->relationship('school', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->label('Sekolah')
                            ->native(false),
                        Forms\Components\Select::make('building_id')
                            ->relationship('building', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->label('Gedung')
                            ->native(false),
                        Forms\Components\Select::make('room_ref_id')
                            ->relationship('reference', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->label('Jenis Ruangan')
                            ->native(false),
                        Forms\Components\TextInput::make('code')
                            ->label('Kode Ruangan')
                            ->maxLength(20)
                            ->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(100)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $operation, $state, Forms\Set $set) {
                                if ($operation !== 'create') return;
                                $set('code', Str::slug($state));
                            })
                            ->label('Nama Ruangan'),
                        Forms\Components\TextInput::make('reg_no')
                            ->label('Nomor Registrasi')
                            ->maxLength(50),
                    ])->columns(2),

                Forms\Components\Section::make('Spesifikasi Fisik')
                    ->schema([
                        Forms\Components\TextInput::make('floor')
                            ->label('Lantai')
                            ->numeric()
                            ->minValue(0),
                        Forms\Components\TextInput::make('length')
                            ->label('Panjang (m)')
                            ->numeric()
                            ->step(0.01),
                        Forms\Components\TextInput::make('width')
                            ->label('Lebar (m)')
                            ->numeric()
                            ->step(0.01),
                        Forms\Components\TextInput::make('area')
                            ->label('Luas (m²)')
                            ->numeric()
                            ->step(0.01),
                        Forms\Components\TextInput::make('capacity')
                            ->label('Kapasitas (orang)')
                            ->numeric()
                            ->minValue(1),
                    ])->columns(3),

                Forms\Components\Section::make('Detail Teknis')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        Forms\Components\TextInput::make('plaster_area')
                            ->label('Luas Plester (m²)')
                            ->numeric()
                            ->step(0.01),
                        Forms\Components\TextInput::make('ceiling_area')
                            ->label('Luas Plafon (m²)')
                            ->numeric()
                            ->step(0.01),
                        Forms\Components\TextInput::make('wall_area')
                            ->label('Luas Dinding (m²)')
                            ->numeric()
                            ->step(0.01),
                        Forms\Components\TextInput::make('window_area')
                            ->label('Luas Jendela (m²)')
                            ->numeric()
                            ->step(0.01),
                        Forms\Components\TextInput::make('door_area')
                            ->label('Luas Pintu (m²)')
                            ->numeric()
                            ->step(0.01),
                        Forms\Components\TextInput::make('frame_len')
                            ->label('Panjang Kusen (m)')
                            ->numeric()
                            ->step(0.01),
                        Forms\Components\TextInput::make('floor_area')
                            ->label('Luas Lantai (m²)')
                            ->numeric()
                            ->step(0.01),
                        Forms\Components\TextInput::make('elec_area')
                            ->label('Luas Instalasi Listrik (m²)')
                            ->numeric()
                            ->step(0.01),
                        Forms\Components\TextInput::make('elec_points')
                            ->label('Jumlah Titik Listrik')
                            ->numeric(),
                        Forms\Components\TextInput::make('water_len')
                            ->label('Panjang Instalasi Air (m)')
                            ->numeric()
                            ->step(0.01),
                        Forms\Components\TextInput::make('water_points')
                            ->label('Jumlah Titik Air')
                            ->numeric(),
                        Forms\Components\TextInput::make('drain_len')
                            ->label('Panjang Saluran Pembuangan (m)')
                            ->numeric(),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Kode')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Ruangan')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('school.name')
                    ->label('Sekolah')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('building.name')
                    ->label('Gedung')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('reference.name')
                    ->label('Jenis')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('floor')
                    ->label('Lantai')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('area')
                    ->label('Luas (m²)')
                    ->numeric(decimalPlaces: 2)
                    ->sortable(),
                Tables\Columns\TextColumn::make('capacity')
                    ->label('Kapasitas')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('conditions_count')
                    ->label('Kondisi')
                    ->counts('conditions')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('documents_count')
                    ->label('Dokumen')
                    ->counts('documents')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('school')
                    ->relationship('school', 'name')
                    ->label('Sekolah')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('building')
                    ->relationship('building', 'name')
                    ->label('Gedung')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('reference')
                    ->relationship('reference', 'name')
                    ->label('Jenis Ruangan')
                    ->searchable()
                    ->preload(),
                Tables\Filters\Filter::make('has_capacity')
                    ->form([
                        Forms\Components\TextInput::make('min_capacity')
                            ->label('Kapasitas Minimal')
                            ->numeric()
                            ->minValue(1),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['min_capacity'],
                                fn(Builder $query, $min) => $query->where('capacity', '>=', $min)
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    // Custom bulk action example
                    Tables\Actions\BulkAction::make('updateCapacity')
                        ->label('Update Kapasitas')
                        ->form([
                            Forms\Components\TextInput::make('new_capacity')
                                ->label('Kapasitas Baru')
                                ->required()
                                ->numeric()
                                ->minValue(1),
                        ])
                        ->action(function (array $data, $records) {
                            foreach ($records as $record) {
                                $record->update(['capacity' => $data['new_capacity']]);
                            }
                        })
                        ->icon('heroicon-o-user-group'),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
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
            'index' => Pages\ListRooms::route('/'),
            'create' => Pages\CreateRoom::route('/create'),
            'edit' => Pages\EditRoom::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['school', 'building', 'reference'])
            ->withCount(['conditions', 'documents']);
    }
}
