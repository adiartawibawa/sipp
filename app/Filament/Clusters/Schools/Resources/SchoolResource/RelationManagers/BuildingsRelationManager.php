<?php

namespace App\Filament\Clusters\Schools\Resources\SchoolResource\RelationManagers;

use App\Models\Schools\Building;
use App\Models\Schools\InfraCategory;
use App\Models\Schools\InfraCondition;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Grid;
use Filament\Forms\Form;
use Filament\Forms\Get;
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
                    ->maxLength(255),

                Forms\Components\Select::make('land_id')
                    ->label('Berdiri diatas Tanah')
                    ->relationship(
                        name: 'land',
                        titleAttribute: 'name',
                    )
                    ->required(),

                Forms\Components\Select::make('infra_cat_id')
                    ->label('Kategori Bangunan')
                    ->relationship(
                        name: 'category',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn(Builder $query) => $query->whereIn('type', ['building', 'other'])
                    )
                    ->searchable()
                    ->preload()
                    ->required()
                    ->live()
                    ->afterStateUpdated(function (Get $get, Set $set) {
                        // Reset code ketika kategori diubah
                        if ($get('infra_cat_id')) {
                            $category = InfraCategory::find($get('infra_cat_id'));
                            $count = Building::where('infra_cat_id', $category->id)->count();
                            $set('code', $category->code . '-' . ($count + 1));
                        }
                    })
                    ->getOptionLabelFromRecordUsing(fn(InfraCategory $record) => "{$record->code} - {$record->name}"),

                // Input Kode Bangunan
                Forms\Components\TextInput::make('code')
                    ->label('Kode Bangunan')
                    ->maxLength(50)
                    ->required()
                    ->default(function (Get $get, Set $set) {
                        if (!$get('infra_cat_id')) {
                            return null;
                        }

                        $category = InfraCategory::find($get('infra_cat_id'));
                        $count = Building::where('infra_cat_id', $category->id)->count();
                        return $category->code . '-' . ($count + 1);
                    })
                    ->disabled(fn(Get $get): bool => !$get('infra_cat_id'))
                    ->dehydrated()
                    ->helperText(function (Get $get) {
                        if (!$get('infra_cat_id')) {
                            return 'Pilih kategori bangunan terlebih dahulu';
                        }
                        return 'Format: [Kode Kategori]-[Nomor/Abjad]';
                    }),

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
                    ->step(0.01)
                    ->required(),

                Forms\Components\Select::make('ownership')
                    ->label('Kepemilikan')
                    ->options([
                        'milik_sendiri' => 'Milik Sendiri',
                        'sewa' => 'Sewa',
                        'pinjam_pakai' => 'Pinjam Pakai',
                    ]),

                Forms\Components\Select::make('borrow_status')
                    ->label('Status Pinjaman')
                    ->options([
                        'digunakan' => 'Digunakan',
                        'tidak_digunakan' => 'Tidak Digunakan',
                        'dipinjamkan' => 'Dipinjamkan',
                    ]),

                Forms\Components\TextInput::make('asset_value')
                    ->label('Nilai Aset')
                    ->numeric()
                    ->step(0.01),

                Forms\Components\TextInput::make('floors')
                    ->label('Jumlah Lantai')
                    ->integer(),

                Forms\Components\TextInput::make('build_year')
                    ->label('Tahun Pembangunan')
                    ->numeric()
                    ->minValue(1900)
                    ->maxValue(now()->year),

                Forms\Components\DatePicker::make('permit_date')
                    ->label('Tanggal IMB'),

                Forms\Components\Textarea::make('notes')
                    ->label('Catatan')
                    ->columnSpanFull(),

            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Kode')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Bangunan')
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

                Tables\Columns\TextColumn::make('ownership')
                    ->label('Kepemilikan')
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'milik_sendiri' => 'Milik Sendiri',
                        'sewa' => 'Sewa',
                        'pinjam_pakai' => 'Pinjam Pakai',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('latestCondition.condition')
                    ->label('Kondisi')
                    ->badge()
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'good' => 'success',
                        'light_damage' => 'info',
                        'medium_damage' => 'warning',
                        'heavy_damage' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'good' => 'Baik',
                        'light_damage' => 'Rusak Ringan',
                        'medium_damage' => 'Rusak Sedang',
                        'heavy_damage' => 'Rusak Berat',
                        default => ucfirst(str_replace('_', ' ', $state)),
                    }),

                Tables\Columns\TextColumn::make('build_year')
                    ->label('Tahun Dibangun')
                    ->sortable(),

                Tables\Columns\TextColumn::make('building_age')
                    ->label('Usia Bangunan')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('ownership')
                    ->label('Kepemilikan')
                    ->options([
                        'milik_sendiri' => 'Milik Sendiri',
                        'sewa' => 'Sewa',
                        'pinjam_pakai' => 'Pinjam Pakai',
                    ]),

                Tables\Filters\SelectFilter::make('conditions.condition')
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
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\Action::make('manage_conditions')
                        ->label('Kondisi')
                        ->icon('heroicon-m-wrench-screwdriver')
                        ->modalHeading('Kelola Kondisi Bangunan')
                        ->action(function (Building $record, array $data): void {
                            $record->conditions()->create([
                                'condition' => $data['condition'],
                                'percentage' => $data['percentage'],
                                'notes' => $data['notes'],
                                'checked_at' => $data['checked_at'],
                            ]);
                        })
                        ->form(fn(Building $record) => [
                            Grid::make(2)
                                ->schema([
                                    Forms\Components\Select::make('condition')
                                        ->label('Kondisi')
                                        ->options(
                                            collect(InfraCondition::defaultInfraCondition())
                                                ->mapWithKeys(fn($data) => [
                                                    $data['slug'] => sprintf(
                                                        "%s (%d%%) - %s",
                                                        ucwords(str_replace('_', ' ', $data['condition'] ?? '')),
                                                        $data['percentage'] ?? 0,
                                                        $data['notes'] ?? ''
                                                    )
                                                ])
                                                ->toArray()
                                        )
                                        ->required()
                                        ->live()
                                        ->afterStateUpdated(function ($state, Set $set) {
                                            $selected = collect(InfraCondition::defaultInfraCondition())
                                                ->firstWhere('slug', $state);
                                            if ($selected) {
                                                $set('percentage', $selected['percentage']);
                                                $set('notes', $selected['notes']);
                                            }
                                        }),

                                    Forms\Components\TextInput::make('percentage')
                                        ->numeric()
                                        ->readOnly(),

                                    Forms\Components\Textarea::make('notes')
                                        ->readOnly(),

                                    Forms\Components\DatePicker::make('checked_at')
                                        ->default(now()),
                                ]),
                            // Optional: Tambahkan daftar kondisi
                            Forms\Components\View::make('filament.clusters.schools.resources.school.modals.building-conditions-table')
                                ->columnSpanFull()
                                ->viewData(['building' => $record]),
                        ])
                        ->modalWidth('4xl'),
                    Tables\Actions\DeleteAction::make(),
                ]),
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
