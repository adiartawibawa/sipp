<?php

namespace App\Filament\Clusters\Schools\Resources;

use App\Filament\Clusters\Schools;
use App\Filament\Clusters\Schools\Resources\FacilityConditionResource\Pages;
use App\Filament\Clusters\Schools\Resources\FacilityConditionResource\RelationManagers;
use App\Models\Schools\FacilityCondition;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

class FacilityConditionResource extends Resource
{
    protected static ?string $model = FacilityCondition::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?string $cluster = Schools::class;

    protected static ?string $modelLabel = 'Kondisi Fasilitas';

    protected static ?string $pluralModelLabel = 'Kondisi Fasilitas';

    protected static ?string $navigationLabel = 'Kondisi Fasilitas';

    protected static ?string $navigationGroup = 'Manajemen Infrastruktur';

    protected static ?int $navigationSort = 24;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Kondisi')
                    ->schema([
                        Forms\Components\Select::make('facil_id')
                            ->label('Fasilitas')
                            ->relationship('facility', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->label('Nama Fasilitas')
                                    ->required()
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('code')
                                    ->label('Kode Fasilitas')
                                    ->maxLength(50),
                            ]),

                        Forms\Components\Select::make('condition')
                            ->label('Kondisi')
                            ->options([
                                'good' => 'Baik',
                                'light' => 'Rusak Ringan',
                                'heavy' => 'Rusak Berat',
                            ])
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                $percentage = match ($state) {
                                    'good' => 100,
                                    'light' => 60,
                                    'heavy' => 20,
                                    default => 0
                                };
                                $set('percentage', $percentage);
                                $set('slug', Str::slug($state));
                            }),

                        Forms\Components\TextInput::make('percentage')
                            ->label('Persentase Kondisi (%)')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->step(0.01)
                            ->required()
                            ->suffix('%'),

                        Forms\Components\DatePicker::make('checked_at')
                            ->label('Tanggal Pengecekan')
                            ->required()
                            ->default(now())
                            ->maxDate(now()),

                        Forms\Components\Textarea::make('notes')
                            ->label('Catatan')
                            ->columnSpanFull(),

                        Forms\Components\FileUpload::make('photos')
                            ->label('Dokumentasi Foto')
                            ->image()
                            ->multiple()
                            ->directory('facility-conditions')
                            ->maxFiles(5)
                            ->downloadable()
                            ->openable(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('facility.name')
                    ->label('Nama Fasilitas')
                    ->searchable()
                    ->sortable()
                    ->url(fn($record) => OtherFacilityResource::getUrl('edit', ['record' => $record->facil_id])),

                Tables\Columns\TextColumn::make('condition')
                    ->label('Kondisi')
                    ->formatStateUsing(fn($state) => match ($state) {
                        'good' => 'Baik',
                        'light' => 'Rusak Ringan',
                        'heavy' => 'Rusak Berat',
                        default => $state
                    })
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'good' => 'success',
                        'light' => 'warning',
                        'heavy' => 'danger',
                        default => 'gray'
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('percentage')
                    ->label('Persentase')
                    ->numeric(decimalPlaces: 2)
                    ->suffix('%')
                    ->sortable(),

                Tables\Columns\TextColumn::make('checked_at')
                    ->label('Terakhir Dicek')
                    ->date()
                    ->sortable()
                    ->description(fn($record) => $record->checked_at->diffForHumans()),

                Tables\Columns\ImageColumn::make('photos')
                    ->label('Foto')
                    ->stacked()
                    ->limit(3)
                    ->limitedRemainingText(isSeparate: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('condition')
                    ->label('Kondisi')
                    ->options([
                        'good' => 'Baik',
                        'light' => 'Rusak Ringan',
                        'heavy' => 'Rusak Berat',
                    ]),

                Tables\Filters\SelectFilter::make('facility')
                    ->label('Fasilitas')
                    ->relationship('facility', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\Filter::make('checked_recently')
                    ->label('Dicek dalam 30 hari')
                    ->query(fn(Builder $query) => $query->where('checked_at', '>=', now()->subDays(30)))
                    ->toggle(),

                Tables\Filters\Filter::make('needs_attention')
                    ->label('Perlu Perhatian')
                    ->query(fn(Builder $query) => $query->whereIn('condition', ['light', 'heavy']))
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('check_now')
                    ->label('Periksa Kembali')
                    ->icon('heroicon-m-arrow-path')
                    ->form([
                        Forms\Components\Select::make('condition')
                            ->label('Kondisi Baru')
                            ->options([
                                'good' => 'Baik',
                                'light' => 'Rusak Ringan',
                                'heavy' => 'Rusak Berat',
                            ])
                            ->required(),

                        Forms\Components\Textarea::make('notes')
                            ->label('Catatan Tambahan'),
                    ])
                    ->action(function (FacilityCondition $record, array $data) {
                        $record->update([
                            'condition' => $data['condition'],
                            'percentage' => match ($data['condition']) {
                                'good' => 100,
                                'light' => 60,
                                'heavy' => 20,
                                default => 0
                            },
                            'checked_at' => now(),
                            'notes' => $record->notes ? $record->notes . "\n\n" . now()->format('Y-m-d') . ": " . $data['notes'] : $data['notes']
                        ]);
                    }),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->before(function (Collection $records) {
                            $recordsWithPhotos = $records->filter(fn($record) => !empty($record->photos));

                            if ($recordsWithPhotos->isNotEmpty()) {
                                throw new \Exception('Beberapa kondisi memiliki foto dokumentasi dan tidak dapat dihapus. Hapus foto terlebih dahulu.');
                            }
                        }),

                    Tables\Actions\BulkAction::make('update_check_date')
                        ->label('Perbarui Tanggal Pengecekan')
                        ->icon('heroicon-m-calendar')
                        ->action(function (Collection $records) {
                            $records->each->update(['checked_at' => now()]);
                        })
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // RelationManagers\MaintenanceHistoryRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFacilityConditions::route('/'),
            'create' => Pages\CreateFacilityCondition::route('/create'),
            'edit' => Pages\EditFacilityCondition::route('/{record}/edit'),
        ];
    }
}
