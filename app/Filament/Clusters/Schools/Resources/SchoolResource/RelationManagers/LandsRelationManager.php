<?php

namespace App\Filament\Clusters\Schools\Resources\SchoolResource\RelationManagers;

use App\Livewire\InfraDocuments\DocumentsTable;
use App\Models\Schools\InfraCategory;
use App\Models\Schools\Land;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class LandsRelationManager extends RelationManager
{
    protected static string $relationship = 'lands';

    protected static ?string $title = 'Tanah Sekolah';

    protected static ?string $modelLabel = 'Tanah';

    protected static ?string $pluralModelLabel = 'Daftar Tanah';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('infra_cat_id')
                    ->label('Kategori')
                    ->relationship(
                        name: 'category',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn(Builder $query) => $query->whereIn('type', ['land'])
                    )
                    ->searchable()
                    ->preload()
                    ->required()
                    ->live()
                    ->afterStateUpdated(function (Get $get, Set $set) {
                        // Reset code ketika kategori diubah
                        if ($get('infra_cat_id')) {
                            $category = InfraCategory::find($get('infra_cat_id'));
                            $count = Land::where('infra_cat_id', $category->id)->count();
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
                        $count = Land::where('infra_cat_id', $category->id)->count();
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
                Forms\Components\TextInput::make('name')
                    ->label('Nama Tanah')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('cert_no')
                    ->label('Nomor Sertifikat')
                    ->maxLength(100),
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
                Forms\Components\TextInput::make('avail_area')
                    ->label('Luas Tersedia (m²)')
                    ->numeric()
                    ->step(0.01),
                Forms\Components\Select::make('ownership')
                    ->label('Status Kepemilikan')
                    ->options([
                        // Kepemilikan Penuh
                        '1' => 'Milik Sendiri',
                        '2' => 'Milik Yayasan',
                        // Kepemilikan Pemerintah
                        '3' => 'Milik Pemerintah Daerah',
                        '4' => 'Milik Pemerintah Pusat',
                        '5' => 'Milik Negara (selain Kemdikbud)',
                        // Kepemilikan Khusus Pendidikan
                        '6' => 'Milik Kementerian Agama',
                        '7' => 'Milik PTN/BHMN',
                        // Penggunaan Tidak Tetap
                        '8' => 'Sewa',
                        '9' => 'Pinjam Pakai',
                        '10' => 'Hibah',
                        // Status Khusus
                        '11' => 'Tanpa Bukti Kepemilikan',
                        '12' => 'Lainnya'
                    ]),
                Forms\Components\TextInput::make('njop')
                    ->label('Nilai Jual Objek Pajak (NJOP)')
                    ->prefix('Rp')
                    ->mask(RawJs::make(<<<'JS'
                    $input => {
                        // Hapus semua karakter non-digit kecuali koma
                        let digits = $input.replace(/[^\d,]/g, '');

                        // Pisahkan bagian integer dan desimal
                        let [integer, decimal] = digits.split(',');

                        // Format bagian integer dengan titik sebagai pemisah ribuan
                        if (integer) {
                            integer = integer.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                        }

                        // Gabungkan dengan bagian desimal (maksimal 2 digit)
                        let formatted = integer || '';
                        if (decimal !== undefined) {
                            formatted += ',' + decimal.substring(0, 2);
                        }

                        return formatted;
                    }
                JS))
                    ->rules(['numeric', 'min:0'])
                    ->stripCharacters(['Rp', '.', ','])
                    ->dehydrateStateUsing(function ($state) {
                        // Konversi ke format numerik untuk database
                        return str_replace(['.', ','], ['', '.'], $state);
                    }),
                Forms\Components\Textarea::make('notes')
                    ->label('Keterangan')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('certificate_no')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Tanah')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('cert_no')
                    ->label('No. Sertifikat')
                    ->searchable(),
                Tables\Columns\TextColumn::make('area')
                    ->label('Luas (m²)')
                    ->numeric(decimalPlaces: 2)
                    ->sortable(),
                Tables\Columns\TextColumn::make('avail_area')
                    ->label('Luas Tersedia (m²)')
                    ->numeric(decimalPlaces: 2),
                Tables\Columns\TextColumn::make('ownership')
                    ->label('Status Kepemilikan')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        '1' => 'Milik Sendiri',
                        '2' => 'Milik Yayasan',
                        '3' => 'Milik Pemda',
                        '4' => 'Milik Pusat',
                        '5' => 'Milik Negara',
                        '6' => 'Milik Kemenag',
                        '7' => 'Milik PTN',
                        '8' => 'Sewa',
                        '9' => 'Pinjam Pakai',
                        '10' => 'Hibah',
                        '11' => 'Tanpa Bukti',
                        '12' => 'Lainnya',
                        default => 'Tidak Diketahui',
                    })
                    ->color(fn(string $state): string => match ($state) {
                        // Milik Institusi Pendidikan
                        '1', '2' => 'success', // Hijau untuk kepemilikan sekolah/yayasan
                        // Milik Pemerintah
                        '3', '4', '5', '6', '7' => 'primary', // Biru untuk aset pemerintah
                        // Penggunaan Tidak Tetap
                        '8', '9', '10' => 'warning', // Kuning untuk sewa/hibah
                        // Status Bermasalah
                        '11', '12' => 'danger', // Merah untuk status bermasalah
                        default => 'gray',
                    })
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('njop')
                    ->label('NJOP')
                    ->formatStateUsing(function ($state) {
                        $formatted = number_format($state, 2, ',', '.');
                        return 'IDR ' . $formatted;
                    })
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('ownership')
                    ->label('Status Kepemilikan')
                    ->options([
                        // Kepemilikan Penuh
                        '1' => 'Milik Sendiri',
                        '2' => 'Milik Yayasan',
                        // Kepemilikan Pemerintah
                        '3' => 'Milik Pemerintah Daerah',
                        '4' => 'Milik Pemerintah Pusat',
                        '5' => 'Milik Negara (selain Kemdikbud)',
                        // Kepemilikan Khusus Pendidikan
                        '6' => 'Milik Kementerian Agama',
                        '7' => 'Milik PTN/BHMN',
                        // Penggunaan Tidak Tetap
                        '8' => 'Sewa',
                        '9' => 'Pinjam Pakai',
                        '10' => 'Hibah',
                        // Status Khusus
                        '11' => 'Tanpa Bukti Kepemilikan',
                        '12' => 'Lainnya'
                    ]),
                Tables\Filters\Filter::make('has_certificate')
                    ->label('Hanya yang memiliki sertifikat')
                    ->query(fn(Builder $query): Builder => $query->whereNotNull('cert_no')),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\Action::make('Dokumen')
                        ->icon('heroicon-o-document-check')
                        ->modalHeading('Kelola Dokumen Tanah')
                        ->modalSubmitActionLabel('Simpan')
                        ->form(function ($record) {
                            return [
                                Forms\Components\TextInput::make('name')->required()->label('Nama Dokumen'),
                                Forms\Components\FileUpload::make('path')
                                    ->disk('public')
                                    ->directory('infra_docs')
                                    ->required()
                                    ->label('Upload Dokumen'),
                                Forms\Components\Section::make('Dokumen Terlampir')
                                    ->schema([
                                        Forms\Components\Livewire::make(DocumentsTable::class, [
                                            'record' => $record,
                                        ]),
                                    ]),
                            ];
                        })
                        ->action(function (array $data, $record) {
                            $record->documents()->create($data);
                        }),
                    Tables\Actions\Action::make('Riwayat Perolehan')->icon('heroicon-o-paper-clip'),
                    Tables\Actions\Action::make('Status Hukum')->icon('heroicon-o-scale'),
                    Tables\Actions\DeleteAction::make(),
                ])
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
