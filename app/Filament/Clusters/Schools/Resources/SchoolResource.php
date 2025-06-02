<?php

namespace App\Filament\Clusters\Schools\Resources;

use App\Exports\SchoolsExport;
use App\Filament\Clusters\Schools;
use App\Filament\Clusters\Schools\Resources\SchoolResource\Pages;
use App\Filament\Clusters\Schools\Resources\SchoolResource\RelationManagers;
use App\Models\Schools\School;
use App\Models\Regions\Province;
use App\Models\Regions\Regency;
use App\Models\Regions\District;
use App\Models\Regions\Village;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;
use Filament\Forms\Set;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class SchoolResource extends Resource
{
    protected static ?string $model = School::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-library';

    protected static ?string $cluster = Schools::class;

    protected static ?string $navigationLabel = 'Sekolah';

    protected static ?string $modelLabel = 'Sekolah';

    protected static ?string $pluralModelLabel = 'Data Sekolah';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('SchoolTabs')
                    ->tabs([
                        Tabs\Tab::make('Informasi Umum')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        Section::make('Identitas Sekolah')
                                            ->schema([
                                                Forms\Components\TextInput::make('npsn')
                                                    ->label('NPSN - Nomor Pokok Sekolah Nasional')
                                                    ->unique(ignoreRecord: true)
                                                    ->maxLength(8)
                                                    ->numeric()
                                                    ->length(8),
                                                Forms\Components\TextInput::make('name')
                                                    ->label('Nama Sekolah')
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
                                                Forms\Components\TextInput::make('nss')
                                                    ->label('NSS - Nomor Statistik Sekolah')
                                                    ->maxLength(20),
                                                Forms\Components\Select::make('edu_type')
                                                    ->label('Jenjang Pendidikan')
                                                    ->options(
                                                        collect(School::defaultEduType())
                                                            ->mapWithKeys(fn($item) => [
                                                                $item['code'] => "{$item['code']} - {$item['name']}"
                                                            ])
                                                            ->toArray()
                                                    )
                                                    ->required(),
                                                Forms\Components\Select::make('status')
                                                    ->label('Status Sekolah')
                                                    ->options([
                                                        'negeri' => 'Negeri',
                                                        'swasta' => 'Swasta',
                                                    ])
                                                    ->required(),
                                            ]),
                                        Section::make('Legalitas')
                                            ->schema([
                                                Forms\Components\TextInput::make('est_year')
                                                    ->label('Tahun Berdiri')
                                                    ->numeric()
                                                    ->minValue(1900)
                                                    ->maxValue(Carbon::now()->year),
                                                SpatieMediaLibraryFileUpload::make('decree_file')
                                                    ->label('File SK Pendirian Sekolah')
                                                    ->collection('school_decrees')
                                                    ->acceptedFileTypes(['application/pdf'])
                                                    ->maxSize(1024 * 5)
                                                    ->openable(true),
                                                Forms\Components\TextInput::make('op_permit_no')
                                                    ->label('Nomor Izin Operasional')
                                                    ->maxLength(100),
                                                Forms\Components\DatePicker::make('op_permit_date')
                                                    ->label('Tanggal Izin Operasional')->columnSpan(2),
                                                Forms\Components\Select::make('accreditation')
                                                    ->label('Akreditasi')
                                                    ->options([
                                                        'A' => 'A',
                                                        'B' => 'B',
                                                        'C' => 'C',
                                                        'D' => 'D',
                                                        'Tidak Terakreditasi' => 'Tidak Terakreditasi',
                                                    ]),
                                                Forms\Components\TextInput::make('accred_score')
                                                    ->label('Nilai Akreditasi')
                                                    ->numeric()
                                                    ->step(0.01),
                                                Forms\Components\TextInput::make('accred_year')
                                                    ->label('Tahun Akreditasi')
                                                    ->numeric()
                                                    ->minValue(1900)
                                                    ->maxValue(Carbon::now()->year),
                                                Forms\Components\Select::make('curriculum')
                                                    ->label('Kurikulum')
                                                    ->options(
                                                        collect(School::defaultCuriculum())
                                                            ->mapWithKeys(fn($item) => [
                                                                $item['code'] => "{$item['code']} - {$item['name']}"
                                                            ])
                                                            ->toArray()
                                                    ),
                                            ]),
                                    ]),
                            ]),
                        Tabs\Tab::make('Lokasi')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        Section::make('Alamat')
                                            ->schema([
                                                Forms\Components\Select::make('province_id')
                                                    ->label('Provinsi')
                                                    ->options(Province::all()->pluck('name', 'id'))
                                                    ->searchable()
                                                    ->preload()
                                                    ->live()
                                                    ->afterStateUpdated(function (callable $set) {
                                                        $set('regency_id', null);
                                                        $set('district_id', null);
                                                        $set('village_id', null);
                                                    }),
                                                Forms\Components\Select::make('regency_id')
                                                    ->label('Kabupaten/Kota')
                                                    ->options(function (callable $get) {
                                                        $provinceId = $get('province_id');
                                                        if (!$provinceId) {
                                                            return [];
                                                        }
                                                        return Regency::where('province_id', $provinceId)->pluck('name', 'id');
                                                    })
                                                    ->searchable()
                                                    ->preload()
                                                    ->live()
                                                    ->afterStateUpdated(fn(callable $set) => $set('district_id', null)),
                                                Forms\Components\Select::make('district_id')
                                                    ->label('Kecamatan')
                                                    ->options(function (callable $get) {
                                                        $regencyId = $get('regency_id');
                                                        if (!$regencyId) {
                                                            return [];
                                                        }
                                                        return District::where('regency_id', $regencyId)->pluck('name', 'id');
                                                    })
                                                    ->searchable()
                                                    ->preload()
                                                    ->live()
                                                    ->afterStateUpdated(fn(callable $set) => $set('village_id', null)),
                                                Forms\Components\Select::make('village_id')
                                                    ->label('Desa/Kelurahan')
                                                    ->options(function (callable $get) {
                                                        $districtId = $get('district_id');
                                                        if (!$districtId) {
                                                            return [];
                                                        }
                                                        return Village::where('district_id', $districtId)->pluck('name', 'id');
                                                    })
                                                    ->searchable()
                                                    ->preload(),
                                                Forms\Components\TextInput::make('postal_code')
                                                    ->label('Kode Pos')
                                                    ->maxLength(10),
                                                Forms\Components\Textarea::make('address')
                                                    ->label('Alamat Lengkap')
                                                    ->columnSpanFull(),
                                            ]),
                                        Section::make('Koordinat')
                                            ->schema([
                                                Forms\Components\TextInput::make('latitude')
                                                    ->label('Latitude')
                                                    ->numeric()
                                                    ->step(0.000001),
                                                Forms\Components\TextInput::make('longitude')
                                                    ->label('Longitude')
                                                    ->numeric()
                                                    ->step(0.000001),
                                                Forms\Components\TextInput::make('full_address')
                                                    ->label('Alamat Lengkap')
                                                    ->columnSpanFull()
                                                    ->disabled(),
                                            ]),
                                    ]),
                            ]),
                        Tabs\Tab::make('Foto & Media')
                            ->schema([
                                SpatieMediaLibraryFileUpload::make('photo')
                                    ->label('Foto/Video Sekolah')
                                    ->collection('school_galleries')
                                    ->acceptedFileTypes(['image/*'])
                                    ->maxSize(1024 * 5)
                                    ->multiple()
                                    ->reorderable()
                                    ->openable(true)
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('npsn')
                    ->label('NPSN')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Nama Sekolah')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn(School $record) => Str::upper("{$record->edu_type} - {$record->status}")),
                TextColumn::make('village.name')
                    ->label('Desa/Kelurahan')
                    ->size('sm'),
                TextColumn::make('district.name')
                    ->label('Kecamatan')
                    ->size('sm'),
                TextColumn::make('regency.name')
                    ->label('Kabupaten/Kota')
                    ->size('sm'),
                TextColumn::make('accreditation')
                    ->label('Akreditasi')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'A' => 'success',
                        'B' => 'primary',
                        'C' => 'warning',
                        'D' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('buildings_count')
                    ->label('Bangunan')
                    ->counts('buildings')
                    ->badge(),
                TextColumn::make('lands_count')
                    ->label('Tanah')
                    ->counts('lands')
                    ->badge(),
            ])

            ->filters([
                SelectFilter::make('status')
                    ->label('Status Sekolah')
                    ->options([
                        'negeri' => 'Negeri',
                        'swasta' => 'Swasta',
                    ]),
                SelectFilter::make('edu_type')
                    ->label('Jenjang Pendidikan')
                    ->options(
                        collect(School::defaultEduType())
                            ->mapWithKeys(fn($item) => [
                                $item['code'] => "{$item['code']} - {$item['name']}"
                            ])
                            ->toArray()
                    ),
                SelectFilter::make('accreditation')
                    ->label('Akreditasi')
                    ->options([
                        'A' => 'A',
                        'B' => 'B',
                        'C' => 'C',
                        'D' => 'D',
                        'Tidak Terakreditasi' => 'Tidak Terakreditasi',
                    ]),
                Filter::make('has_coordinate')
                    ->label('Memiliki Koordinat')
                    ->query(fn(Builder $query): Builder => $query->whereNotNull('latitude')->whereNotNull('longitude')),
                SelectFilter::make('province_id')
                    ->label('Provinsi')
                    ->relationship('province', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('regency_id')
                    ->label('Kabupaten/Kota')
                    ->relationship('regency', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('export')
                        ->label('Export Selected')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->action(function ($records) {
                            return Excel::download(new SchoolsExport($records), 'sekolah-export.xlsx');
                        }),
                ]),
            ])
            ->headerActions([
                Tables\Actions\Action::make('exportAll')
                    ->label('Export All')
                    ->action(function () {
                        return Excel::download(new SchoolsExport(), 'semua-sekolah-export.xlsx');
                    })
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->defaultSort('district_id', 'asc');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\BuildingsRelationManager::class,
            RelationManagers\LandsRelationManager::class,
            RelationManagers\RoomsRelationManager::class,
            RelationManagers\OtherFacilitiesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSchools::route('/'),
            'create' => Pages\CreateSchool::route('/create'),
            'edit' => Pages\EditSchool::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['province', 'regency', 'district', 'village'])
            ->withCount(['buildings', 'lands', 'rooms', 'otherFacilities']);
    }
}

// --- Integrasi GIS ---
// TODO: Tambahkan peta interaktif di dashboard (gunakan Leaflet atau Google Maps)
// TODO: Gunakan package seperti Leaflet atau Google Maps untuk visualisasi geospasial

// --- Notifikasi ---
// TODO: Tambahkan alert untuk fasilitas yang perlu perbaikan
// TODO: Buat notifikasi untuk akreditasi yang akan kadaluarsa

// --- Custom Filters ---
// TODO: Buat filter kompleks untuk analisis data sekolah
// TODO: Tambahkan filter berdasarkan range luas tanah/bangunan

// --- Export Laporan ---
// TODO: Develop fitur export PDF/Excel untuk data sekolah lengkap
// TODO: Buat template laporan yang profesional untuk export

// --- Mobile Optimization ---
// TODO: Sesuaikan tampilan untuk perangkat mobile
// TODO: Tambahkan fitur scan QR code untuk akses cepat ke data sekolah

// --- API Integration ---
// TODO: Hubungkan dengan data pokok pendidikan (Dapodik)
// TODO: Buat sync otomatis untuk data tertentu
