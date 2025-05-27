<?php

namespace App\Filament\Clusters\Schools\Resources;

use App\Filament\Clusters\Schools;
use App\Filament\Clusters\Schools\Resources\BuildingResource\Pages;
use App\Filament\Clusters\Schools\Resources\BuildingResource\RelationManagers;
use App\Models\Schools\Building;
use App\Models\Schools\InfraCategory;
use App\Models\Schools\Land;
use App\Models\Schools\School;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BuildingResource extends Resource
{
    protected static ?string $model = Building::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';

    protected static ?string $cluster = Schools::class;

    protected static ?string $modelLabel = 'Bangunan';

    protected static ?string $pluralModelLabel = 'Data Bangunan';

    protected static ?string $navigationGroup = 'Fasilitas Sekolah';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Utama')
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
                            ->options(InfraCategory::all()->pluck('name', 'id'))
                            ->searchable()
                            ->native(false),

                        Forms\Components\Select::make('land_id')
                            ->label('Tanah')
                            ->relationship('land', 'id')
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->helperText('Tanah tempat bangunan berdiri'),

                        Forms\Components\TextInput::make('code')
                            ->label('Kode Bangunan')
                            ->maxLength(20)
                            ->unique(ignoreRecord: true)
                            ->helperText('Kode unik untuk identifikasi bangunan'),

                        Forms\Components\TextInput::make('name')
                            ->label('Nama Bangunan')
                            ->required()
                            ->maxLength(100)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $operation, $state, Forms\Set $set) {
                                if ($operation !== 'create') {
                                    return;
                                }

                                $set('code', Str::slug($state));
                            }),

                        Forms\Components\TextInput::make('ownership')
                            ->label('Kepemilikan')
                            ->maxLength(50)
                            ->helperText('Status kepemilikan bangunan (contoh: Milik Sendiri, Sewa)'),
                    ])->columns(2),

                Forms\Components\Section::make('Dimensi Bangunan')
                    ->schema([
                        Forms\Components\TextInput::make('length')
                            ->label('Panjang (m)')
                            ->numeric()
                            ->step(0.01)
                            ->maxValue(9999.99),

                        Forms\Components\TextInput::make('width')
                            ->label('Lebar (m)')
                            ->numeric()
                            ->step(0.01)
                            ->maxValue(9999.99),

                        Forms\Components\TextInput::make('area')
                            ->label('Luas (m²)')
                            ->numeric()
                            ->step(0.01)
                            ->maxValue(999999.99)
                            ->helperText('Luas otomatis dihitung dari panjang x lebar jika kosong'),

                        Forms\Components\TextInput::make('floors')
                            ->label('Jumlah Lantai')
                            ->integer()
                            ->minValue(1)
                            ->maxValue(100),
                    ])->columns(2),

                Forms\Components\Section::make('Informasi Tambahan')
                    ->schema([
                        Forms\Components\TextInput::make('asset_value')
                            ->label('Nilai Aset')
                            ->numeric()
                            ->prefix('Rp')
                            ->step(1000)
                            ->maxValue(999999999999),

                        Forms\Components\DatePicker::make('permit_date')
                            ->label('Tanggal IMB'),

                        Forms\Components\TextInput::make('build_year')
                            ->label('Tahun Dibangun')
                            ->integer()
                            ->minValue(1900)
                            ->maxValue(now()->year),

                        Forms\Components\Select::make('borrow_status')
                            ->label('Status Pinjam')
                            ->options([
                                'permanent' => 'Permanen',
                                'temporary' => 'Sementara',
                                'none' => 'Tidak Dipinjam',
                            ])
                            ->native(false),

                        Forms\Components\Textarea::make('notes')
                            ->label('Catatan')
                            ->columnSpanFull(),
                    ])->columns(2),
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
                    ->label('Nama Bangunan')
                    ->searchable()
                    ->sortable()
                    ->description(fn(Building $record) => $record->category->name ?? '-'),

                Tables\Columns\TextColumn::make('school.name')
                    ->label('Sekolah')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('area')
                    ->label('Luas (m²)')
                    ->numeric(decimalPlaces: 2)
                    ->sortable(),

                Tables\Columns\TextColumn::make('floors')
                    ->label('Lantai')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('asset_value')
                    ->label('Nilai Aset')
                    ->numeric(decimalPlaces: 2)
                    ->prefix('Rp')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('build_year')
                    ->label('Tahun Dibangun')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('rooms_count')
                    ->label('Jumlah Ruangan')
                    ->counts('rooms')
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
                    ->label('Filter Sekolah'),

                Tables\Filters\SelectFilter::make('category')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Filter Kategori'),

                Tables\Filters\TernaryFilter::make('has_land')
                    ->label('Memiliki Tanah')
                    ->placeholder('Semua')
                    ->trueLabel('Dengan Tanah')
                    ->falseLabel('Tanpa Tanah')
                    ->queries(
                        true: fn(Builder $query) => $query->whereNotNull('land_id'),
                        false: fn(Builder $query) => $query->whereNull('land_id'),
                    ),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->before(function (Building $record, Tables\Actions\DeleteAction $action) {
                        if ($record->rooms()->count() > 0) {
                            Notification::make()
                                ->danger()
                                ->title('Tidak Dapat Menghapus')
                                ->body('Bangunan memiliki ruangan terkait. Hapus ruangan terlebih dahulu.')
                                ->persistent()
                                ->send();

                            $action->cancel();
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->before(function (Collection $records, Tables\Actions\DeleteBulkAction $action) {
                            $withRooms = $records->filter(fn($record) => $record->rooms()->count() > 0);

                            if ($withRooms->count() > 0) {
                                Notification::make()
                                    ->danger()
                                    ->title('Tidak Dapat Menghapus ' . $withRooms->count() . ' Bangunan')
                                    ->body('Beberapa bangunan memiliki ruangan terkait.')
                                    ->persistent()
                                    ->send();

                                $action->cancel();
                            }
                        }),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->defaultSort('name');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\RoomsRelationManager::class,
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
            'index' => Pages\ListBuildings::route('/'),
            'create' => Pages\CreateBuilding::route('/create'),
            'edit' => Pages\EditBuilding::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'code', 'school.name'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Sekolah' => $record->school->name,
            'Kategori' => $record->category->name,
        ];
    }
}

// TODO:
// Integrasi Peta:
// Tambahkan field koordinat (lat/long) untuk bangunan
// Integrasi dengan Google Maps/Leaflet untuk visualisasi lokasi

// Reporting:
// Buat laporan kondisi bangunan
// Ekspor data ke Excel/PDF
// Dashboard statistik bangunan

// Workflow Approval:
// Tambahkan sistem approval untuk perubahan data penting
// Riwayat perubahan data (audit trail)

// Maintenance Scheduling:
// Sistem penjadwalan perawatan bangunan
// Notifikasi untuk dokumen yang akan kadaluarsa

// Integrasi dengan Sistem Lain:
// Koneksi ke sistem aset sekolah
// Integrasi dengan sistem inventaris ruangan

// Advanced Search:
// Tambahkan fitur pencarian geospasial
// Filter kompleks berdasarkan multiple criteria

// Backup & Restore:
// Fitur ekspor/impor data
// Backup otomatis data bangunan
