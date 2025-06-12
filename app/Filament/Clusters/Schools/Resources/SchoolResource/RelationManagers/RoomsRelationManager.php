<?php

namespace App\Filament\Clusters\Schools\Resources\SchoolResource\RelationManagers;

use App\Livewire\InfraConditions\ConditionsTable;
use App\Models\Schools\InfraCondition;
use App\Models\Schools\Room;
use App\Models\Schools\RoomReference;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;
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
                Forms\Components\Select::make('building_id')
                    ->label('Bangunan')
                    ->relationship(
                        name: 'building',
                        titleAttribute: 'name',
                    )
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\TextInput::make('name')
                    ->label('Nama Ruangan')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (Set $set, ?string $state) {
                        $set('code', Str::slug($state));
                    }),
                Forms\Components\Select::make('room_ref_id')
                    ->label('Referensi Ruangan')
                    ->relationship(
                        name: 'reference',
                        titleAttribute: 'name'
                    )
                    ->searchable()
                    ->preload()
                    ->required()
                    ->live()
                    ->afterStateUpdated(function (Get $get, Set $set) {
                        // Reset code ketika kategori diubah
                        if ($get('room_ref_id')) {
                            $category = RoomReference::find($get('room_ref_id'));
                            $count = Room::where('room_ref_id', $category->id)->count();
                            $set('code', $category->code . '-' . ($count + 1));
                        }
                    })
                    ->getOptionLabelFromRecordUsing(fn(RoomReference $record) => "{$record->code} - {$record->name}"),

                // Input Kode Ruangan
                Forms\Components\TextInput::make('code')
                    ->label('Kode Ruangan')
                    ->maxLength(50)
                    ->required()
                    ->default(function (Get $get, Set $set) {
                        if (!$get('room_ref_id')) {
                            return null;
                        }

                        $category = RoomReference::find($get('room_ref_id'));
                        $count = Room::where('room_ref_id', $category->id)->count();
                        return $category->code . '-' . ($count + 1);
                    })
                    ->disabled(fn(Get $get): bool => !$get('room_ref_id'))
                    ->dehydrated()
                    ->helperText(function (Get $get) {
                        if (!$get('room_ref_id')) {
                            return 'Pilih kategori ruangan terlebih dahulu';
                        }
                        return 'Format: [Kode Kategori]-[Nomor/Abjad]';
                    }),
                Forms\Components\TextInput::make('capacity')
                    ->label('Kapasitas')
                    ->numeric()
                    ->minValue(1),
                Forms\Components\TextInput::make('length')
                    ->label('Panjang (m)')
                    ->numeric()
                    ->step(0.01)
                    ->live(debounce: 500) // Update 500ms setelah perubahan
                    ->afterStateUpdated(function (Get $get, Set $set) {
                        // Hitung luas ketika panjang diubah
                        $length = $get('length');
                        $width = $get('width');

                        if ($length && $width) {
                            $set('area', round($length * $width, 2));
                        }
                    }),

                Forms\Components\TextInput::make('width')
                    ->label('Lebar (m)')
                    ->numeric()
                    ->step(0.01)
                    ->live(debounce: 500) // Update 500ms setelah perubahan
                    ->afterStateUpdated(function (Get $get, Set $set) {
                        // Hitung luas ketika lebar diubah
                        $length = $get('length');
                        $width = $get('width');

                        if ($length && $width) {
                            $set('area', round($length * $width, 2));
                        }
                    }),

                Forms\Components\TextInput::make('area')
                    ->label('Luas (m²)')
                    ->numeric()
                    ->step(0.01)
                    ->readOnly()
                    ->dehydrated()
                    ->afterStateHydrated(function (Get $get, Set $set) {
                        // Hitung ulang luas ketika data di-load
                        $length = $get('length');
                        $width = $get('width');

                        if ($length && $width) {
                            $set('area', round($length * $width, 2));
                        }

                        if (!$length || !$width) {
                            $set('area', null);
                        }
                    })->suffix('m²'),
                Forms\Components\TextInput::make('floor')
                    ->label('Lantai')
                    ->numeric()
                    ->minValue(0),
                Forms\Components\TextInput::make('reg_no')
                    ->label('Nomor Register')
                    ->maxLength(255),
                // Tambahan field sesuai model
                Forms\Components\TextInput::make('plaster_area')
                    ->label('Luas Plester (m²)')
                    ->numeric()
                    ->step(0.01),
                Forms\Components\TextInput::make('ceiling_area')
                    ->label('Luas Langit-langit (m²)')
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
                    ->label('Panjang Saluran Air (m)')
                    ->numeric(),
            ])
            ->columns(2);
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
                    ->label('Nama Ruangan')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('reference.name')
                    ->label('Jenis Ruangan')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('building.name')
                    ->label('Gedung')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('floor')
                    ->label('Lantai')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('capacity')
                    ->label('Kapasitas')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('area')
                    ->label('Luas (m²)')
                    ->numeric(2)
                    ->sortable(),
                Tables\Columns\TextColumn::make('density')
                    ->label('Kepadatan')
                    ->numeric(2)
                    ->state(function (Room $record): ?float {
                        return $record->density;
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
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('room_ref_id')
                    ->label('Jenis Ruangan')
                    ->relationship('reference', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('building_id')
                    ->label('Gedung')
                    ->relationship('building', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\Filter::make('has_capacity')
                    ->form([
                        Forms\Components\TextInput::make('capacity')
                            ->label('Kapasitas Minimal')
                            ->numeric(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['capacity'],
                                fn(Builder $query, $capacity): Builder => $query->where('capacity', '>=', $capacity),
                            );
                    }),
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
                        ->modalSubmitActionLabel('Simpan')
                        ->modalWidth('7xl')
                        ->form(function (Room $record) {
                            return [
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\Select::make('condition')
                                            ->label('Kondisi')
                                            ->options(
                                                collect(InfraCondition::defaultInfraCondition())
                                                    ->mapWithKeys(fn($data) => [
                                                        $data['slug'] => sprintf(
                                                            "%s (%d%%)",
                                                            ucwords(str_replace('_', ' ', $data['condition'] ?? '')),
                                                            $data['percentage'] ?? 0
                                                        )
                                                    ])
                                                    ->toArray()
                                            )
                                            ->required()
                                            ->live()
                                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                                $selected = collect(InfraCondition::defaultInfraCondition())
                                                    ->firstWhere('slug', $state);
                                                if ($selected) {
                                                    $set('percentage', $selected['percentage']);
                                                    $set('notes', $selected['notes']);
                                                }
                                            }),
                                        Forms\Components\TextInput::make('percentage')
                                            ->numeric()
                                            ->suffix('%')
                                            ->readOnly(),
                                        Forms\Components\Textarea::make('notes')
                                            ->readOnly()
                                            ->columnSpan(1),
                                        Forms\Components\DatePicker::make('checked_at')
                                            ->default(now())
                                            ->required(),
                                        Forms\Components\FileUpload::make('photos')
                                            ->label('Bukti Foto')
                                            ->multiple()
                                            ->image()
                                            ->maxSize(5 * 1024) // 5MB
                                            ->acceptedFileTypes(['image/*'])
                                            ->maxFiles(5)
                                            ->preserveFilenames()
                                            ->directory('temp/condition-uploads') // sementara
                                            ->columnSpanFull(),
                                    ]),

                                Forms\Components\Section::make('Riwayat Kondisi')
                                    ->schema([
                                        Forms\Components\Livewire::make(ConditionsTable::class, [
                                            'record' => $record,
                                        ]),
                                    ]),
                            ];
                        })
                        ->action(function (Room $record, array $data): void {
                            // Simpan kondisi terlebih dahulu
                            $condition = new InfraCondition();
                            $condition->entity()->associate($record); // relasi polimorfik
                            $condition->fill([
                                'condition' => $data['condition'],
                                'slug' => $data['condition'],
                                'percentage' => $data['percentage'],
                                'notes' => $data['notes'],
                                'checked_at' => $data['checked_at'],
                            ]);
                            $condition->save();

                            // Upload dan attach file jika ada
                            if (!empty($data['photos'])) {
                                foreach ($data['photos'] as $photoPath) {
                                    $fullPath = storage_path('app/public/' . $photoPath); // path ke file yang diupload ke storage
                                    try {
                                        if (file_exists($fullPath)) {
                                            $condition->addMedia($fullPath)
                                                ->usingFileName(basename($fullPath))
                                                ->toMediaCollection('condition_photos');

                                            unlink($fullPath);
                                        }
                                    } catch (\Throwable $e) {
                                        Log::warning('Gagal menghapus file sementara: ' . $e->getMessage());
                                    }
                                }
                            }
                        }),
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
