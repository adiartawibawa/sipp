<?php

namespace App\Filament\Clusters\Schools\Resources;

use App\Filament\Clusters\Schools;
use App\Filament\Clusters\Schools\Resources\InfraAcquisitionResource\Pages;
use App\Filament\Clusters\Schools\Resources\InfraAcquisitionResource\RelationManagers;
use App\Models\Schools\InfraAcquisition;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InfraAcquisitionResource extends Resource
{
    protected static ?string $model = InfraAcquisition::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $cluster = Schools::class;

    protected static ?string $modelLabel = 'Perolehan Infrastruktur';

    protected static ?string $pluralModelLabel = 'Daftar Perolehan Infrastruktur';

    protected static ?string $navigationLabel = 'Perolehan Infrastruktur';

    protected static ?string $navigationGroup = 'Legal & Dokumen';

    protected static ?int $navigationSort = 30;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('entity_type')
                    ->label('Jenis Entitas')
                    ->required()
                    ->options([
                        'school' => 'Sekolah',
                        'facility' => 'Fasilitas',
                        'building' => 'Gedung',
                    ])
                    ->live()
                    ->afterStateUpdated(fn($state, Forms\Set $set) => $set('entity_id', null)),

                Forms\Components\Select::make('entity_id')
                    ->label('Entitas')
                    ->required()
                    ->options(function (Forms\Get $get) {
                        $type = $get('entity_type');

                        if (!$type) {
                            return [];
                        }

                        return match ($type) {
                            'school' => \App\Models\Schools\School::all()->pluck('name', 'id'),
                            // 'facility' => \App\Models\Schools\Facility::all()->pluck('name', 'id'),
                            'building' => \App\Models\Schools\Building::all()->pluck('name', 'id'),
                            default => [],
                        };
                    })
                    ->searchable()
                    ->preload(),

                Forms\Components\Select::make('source')
                    ->label('Sumber Perolehan')
                    ->required()
                    ->options([
                        'purchase' => 'Pembelian',
                        'donation' => 'Donasi',
                        'grant' => 'Hibah',
                        'government' => 'Pemerintah',
                        'other' => 'Lainnya',
                    ]),

                Forms\Components\TextInput::make('amount')
                    ->label('Jumlah/Nilai')
                    ->numeric()
                    ->step(0.01)
                    ->suffix('IDR'),

                Forms\Components\TextInput::make('year')
                    ->label('Tahun Perolehan')
                    ->required()
                    ->numeric()
                    ->minValue(2000)
                    ->maxValue(now()->year),

                Forms\Components\Select::make('method')
                    ->label('Metode Perolehan')
                    ->options([
                        'cash' => 'Tunai',
                        'credit' => 'Kredit',
                        'installment' => 'Cicilan',
                        'barter' => 'Tukar Guling',
                    ]),

                Forms\Components\Textarea::make('notes')
                    ->label('Catatan')
                    ->columnSpanFull()
                    ->maxLength(500),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('entity_type')
                    ->label('Jenis Entitas')
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'school' => 'Sekolah',
                        'facility' => 'Fasilitas',
                        'building' => 'Gedung',
                        default => $state,
                    })
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('entity.name')
                    ->label('Nama Entitas')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('source')
                    ->label('Sumber')
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'purchase' => 'Pembelian',
                        'donation' => 'Donasi',
                        'grant' => 'Hibah',
                        'government' => 'Pemerintah',
                        'other' => 'Lainnya',
                        default => $state,
                    })
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Nilai')
                    ->numeric(
                        decimalPlaces: 2,
                        decimalSeparator: ',',
                        thousandsSeparator: '.',
                    )
                    ->money('IDR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('year')
                    ->label('Tahun')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('method')
                    ->label('Metode')
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'cash' => 'Tunai',
                        'credit' => 'Kredit',
                        'installment' => 'Cicilan',
                        'barter' => 'Tukar Guling',
                        default => $state,
                    })
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('source')
                    ->label('Sumber Perolehan')
                    ->options([
                        'purchase' => 'Pembelian',
                        'donation' => 'Donasi',
                        'grant' => 'Hibah',
                        'government' => 'Pemerintah',
                        'other' => 'Lainnya',
                    ]),

                Tables\Filters\SelectFilter::make('entity_type')
                    ->label('Jenis Entitas')
                    ->options([
                        'school' => 'Sekolah',
                        'facility' => 'Fasilitas',
                        'building' => 'Gedung',
                    ]),

                Tables\Filters\Filter::make('year')
                    ->form([
                        Forms\Components\TextInput::make('year_from')
                            ->label('Dari Tahun')
                            ->numeric(),
                        Forms\Components\TextInput::make('year_to')
                            ->label('Sampai Tahun')
                            ->numeric(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['year_from'],
                                fn(Builder $query, $year): Builder => $query->where('year', '>=', $year),
                            )
                            ->when(
                                $data['year_to'],
                                fn(Builder $query, $year): Builder => $query->where('year', '<=', $year),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->before(function (InfraAcquisition $record) {
                        // Validasi sebelum menghapus
                        if ($record->isUsed()) {
                            throw new \Exception('Data perolehan tidak dapat dihapus karena sudah digunakan.');
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    // Custom bulk action untuk ekspor data
                    // Tables\Actions\BulkAction::make('export')
                    //     ->label('Ekspor Data Terpilih')
                    //     ->icon('heroicon-o-arrow-down-tray')
                    //     ->action(fn(Collection $records) => (new InfraAcquisitionExport($records))->download('infra-acquisitions.xlsx')),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // Relation manager akan ditambahkan di sini
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInfraAcquisitions::route('/'),
            'create' => Pages\CreateInfraAcquisition::route('/create'),
            'edit' => Pages\EditInfraAcquisition::route('/{record}/edit'),
        ];
    }

    // public static function getEloquentQuery(): Builder
    // {
    //     return parent::getEloquentQuery()
    //         ->with(['entity'])
    //         ->when(!auth()->user()->isSuperAdmin(), function ($query) {
    //             // Filter data berdasarkan akses user
    //             return $query->whereHas('entity', function ($q) {
    //                 $q->whereIn('school_id', auth()->user()->schools->pluck('id'));
    //             });
    //         });
    // }
}
