<?php

namespace App\Filament\Clusters\Schools\Resources;

use App\Filament\Clusters\Schools;
use App\Filament\Clusters\Schools\Resources\InfraRelocationResource\Pages;
use App\Filament\Clusters\Schools\Resources\InfraRelocationResource\RelationManagers;
use App\Models\Schools\InfraRelocation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InfraRelocationResource extends Resource
{
    protected static ?string $model = InfraRelocation::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';

    protected static ?string $cluster = Schools::class;

    protected static ?string $modelLabel = 'Pemindahan Infrastruktur';

    protected static ?string $pluralModelLabel = 'Daftar Pemindahan Infrastruktur';

    protected static ?string $navigationLabel = 'Pemindahan Infrastruktur';

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
                        'equipment' => 'Peralatan',
                    ])
                    ->live()
                    ->afterStateUpdated(fn($state, Forms\Set $set) => $set('entity_id', null)),

                Forms\Components\Select::make('entity_id')
                    ->label('Entitas yang Dipindahkan')
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
                            // 'equipment' => \App\Models\Schools\Equipment::all()->pluck('name', 'id'),
                            default => [],
                        };
                    })
                    ->searchable()
                    ->preload(),

                Forms\Components\TextInput::make('from')
                    ->label('Dari Lokasi')
                    ->maxLength(255)
                    ->columnSpan(1),

                Forms\Components\TextInput::make('to')
                    ->label('Ke Lokasi')
                    ->required()
                    ->maxLength(255)
                    ->columnSpan(1),

                Forms\Components\DatePicker::make('moved_at')
                    ->label('Tanggal Pemindahan')
                    ->required()
                    ->default(now())
                    ->displayFormat('d/m/Y')
                    ->maxDate(now()),

                Forms\Components\Textarea::make('notes')
                    ->label('Catatan Pemindahan')
                    ->columnSpanFull()
                    ->maxLength(500),
            ])
            ->columns(2);
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
                        'equipment' => 'Peralatan',
                        default => $state,
                    })
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('entity.name')
                    ->label('Nama Entitas')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('full_location')
                    ->label('Lokasi Pemindahan')
                    ->description(fn(InfraRelocation $record) => $record->moved_at->format('d/m/Y'))
                    ->wrap(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dicatat Pada')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('entity_type')
                    ->label('Jenis Entitas')
                    ->options([
                        'school' => 'Sekolah',
                        'facility' => 'Fasilitas',
                        'building' => 'Gedung',
                        'equipment' => 'Peralatan',
                    ]),

                Tables\Filters\Filter::make('moved_at')
                    ->form([
                        Forms\Components\DatePicker::make('moved_from')
                            ->label('Dari Tanggal'),
                        Forms\Components\DatePicker::make('moved_until')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['moved_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('moved_at', '>=', $date),
                            )
                            ->when(
                                $data['moved_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('moved_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->before(function (InfraRelocation $record) {
                        // Validasi sebelum menghapus
                        if ($record->is_recent_move) {
                            throw new \Exception('Data pemindahan yang baru saja dilakukan tidak dapat dihapus.');
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
                    //     ->action(fn(Collection $records) => (new InfraRelocationExport($records))->download('infra-relocations.xlsx')),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // RelationManagers\EntityInfraRelocationsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInfraRelocations::route('/'),
            'create' => Pages\CreateInfraRelocation::route('/create'),
            'edit' => Pages\EditInfraRelocation::route('/{record}/edit'),
        ];
    }

    // public static function getEloquentQuery(): Builder
    // {
    //     return parent::getEloquentQuery()
    //         ->with(['entity'])
    //         ->when(!auth()->user()->can('view_all_infra_relocations'), function ($query) {
    //             // Filter data berdasarkan akses user
    //             return $query->whereHas('entity', function ($q) {
    //                 $q->whereIn('school_id', auth()->user()->managedSchools->pluck('id'));
    //             });
    //         });
    // }
}
