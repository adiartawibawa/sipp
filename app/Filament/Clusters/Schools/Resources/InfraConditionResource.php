<?php

namespace App\Filament\Clusters\Schools\Resources;

use App\Filament\Clusters\Schools;
use App\Filament\Clusters\Schools\Resources\InfraConditionResource\Pages;
use App\Filament\Clusters\Schools\Resources\InfraConditionResource\RelationManagers;
use App\Models\Schools\InfraCondition;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class InfraConditionResource extends Resource
{
    protected static ?string $model = InfraCondition::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?string $cluster = Schools::class;

    protected static ?string $modelLabel = 'Kondisi Infrastruktur';

    protected static ?string $pluralModelLabel = 'Kondisi Infrastruktur';

    protected static ?string $navigationLabel = 'Kondisi Infrastruktur';

    protected static ?string $navigationGroup = 'Manajemen Infrastruktur';

    protected static ?int $navigationSort = 21;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('entity_type')
                    ->label('Jenis Entitas')
                    ->required()
                    ->options([
                        'building' => 'Gedung',
                        'room' => 'Ruangan',
                        'facility' => 'Fasilitas',
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
                            'building' => \App\Models\Schools\Building::all()->pluck('name', 'id'),
                            'room' => \App\Models\Schools\Room::all()->pluck('name', 'id'),
                            // 'facility' => \App\Models\Schools\Facility::all()->pluck('name', 'id'),
                            default => [],
                        };
                    })
                    ->searchable()
                    ->native(false),

                Forms\Components\Select::make('condition')
                    ->label('Kondisi')
                    ->required()
                    ->options([
                        'good' => 'Baik',
                        'light' => 'Rusak Ringan',
                        'heavy' => 'Rusak Berat',
                    ])
                    ->native(false),

                Forms\Components\TextInput::make('percentage')
                    ->label('Persentase Kondisi')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(100)
                    ->step(0.01)
                    ->suffix('%'),

                Forms\Components\DatePicker::make('checked_at')
                    ->label('Tanggal Pemeriksaan')
                    ->required()
                    ->default(now())
                    ->maxDate(now()),

                Forms\Components\TextInput::make('slug')
                    ->label('Slug')
                    ->hidden()
                    ->dehydrated()
                    ->required()
                    ->maxLength(255)
                    ->unique(InfraCondition::class, 'slug', ignoreRecord: true),

                Forms\Components\Textarea::make('notes')
                    ->label('Catatan')
                    ->columnSpanFull(),
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
                        'building' => 'Gedung',
                        'room' => 'Ruangan',
                        'facility' => 'Fasilitas',
                        default => $state,
                    })
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),

                Tables\Columns\TextColumn::make('entity.name')
                    ->label('Nama Entitas')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('condition')
                    ->label('Kondisi')
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'good' => 'Baik',
                        'light' => 'Rusak Ringan',
                        'heavy' => 'Rusak Berat',
                        default => $state,
                    })
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'good' => 'success',
                        'light' => 'warning',
                        'heavy' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('percentage')
                    ->label('Persentase')
                    ->numeric(decimalPlaces: 2)
                    ->suffix('%')
                    ->sortable(),

                Tables\Columns\TextColumn::make('checked_at')
                    ->label('Tanggal Pemeriksaan')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Diupdate')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('condition')
                    ->label('Kondisi')
                    ->options([
                        'good' => 'Baik',
                        'light' => 'Rusak Ringan',
                        'heavy' => 'Rusak Berat',
                    ]),

                Tables\Filters\SelectFilter::make('entity_type')
                    ->label('Jenis Entitas')
                    ->options([
                        'building' => 'Gedung',
                        'room' => 'Ruangan',
                        'facility' => 'Fasilitas',
                    ]),

                Tables\Filters\Filter::make('checked_at')
                    ->label('Periode Pemeriksaan')
                    ->form([
                        Forms\Components\DatePicker::make('checked_from')
                            ->label('Dari Tanggal'),
                        Forms\Components\DatePicker::make('checked_until')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['checked_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('checked_at', '>=', $date),
                            )
                            ->when(
                                $data['checked_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('checked_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->before(function (Model $record, array $data) {
                        if ($record->condition !== $data['condition']) {
                            Notification::make()
                                ->title('Kondisi Diubah')
                                ->body("Kondisi berubah dari {$record->condition} menjadi {$data['condition']}")
                                ->warning()
                                ->send();
                        }
                    }),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    // self::getExportBulkAction(),
                    // self::getImportBulkAction(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // Relation managers akan ditambahkan di sini
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInfraConditions::route('/'),
            'create' => Pages\CreateInfraCondition::route('/create'),
            'edit' => Pages\EditInfraCondition::route('/{record}/edit'),
        ];
    }

    // protected static function getImportBulkAction(): Tables\Actions\BulkAction
    // {
    //     return Tables\Actions\ImportBulkAction::make()
    //         ->importer(InfraConditionImporter::class)
    //         ->icon('heroicon-o-arrow-up-tray')
    //         ->color('primary');
    // }

    // protected static function getExportBulkAction(): Tables\Actions\BulkAction
    // {
    //     return Tables\Actions\ExportBulkAction::make()
    //         ->exporter(InfraConditionExporter::class)
    //         ->icon('heroicon-o-arrow-down-tray')
    //         ->color('success');
    // }

    // public static function getSlug(Model $record): string
    // {
    //     return Str::slug($record->entity->name . '-' . $record->condition . '-' . $record->checked_at->format('Y-m-d'));
    // }
}
