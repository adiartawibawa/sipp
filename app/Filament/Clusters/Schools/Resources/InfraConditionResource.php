<?php

namespace App\Filament\Clusters\Schools\Resources;

use App\Filament\Clusters\Schools;
use App\Filament\Clusters\Schools\Resources\InfraConditionResource\Pages;
use App\Models\Schools\InfraCondition;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
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

    protected static ?int $navigationSort = 23;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Kondisi')
                    ->schema([
                        Forms\Components\MorphToSelect::make('entity')
                            ->label('Entitas Terkait')
                            ->types([
                                Forms\Components\MorphToSelect\Type::make(\App\Models\Schools\Building::class)
                                    ->titleAttribute('name'),
                                Forms\Components\MorphToSelect\Type::make(\App\Models\Schools\Land::class)
                                    ->titleAttribute('name'),
                            ])
                            ->required()
                            ->searchable()
                            ->preload(),

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
                            ->directory('infra-conditions')
                            ->maxFiles(5)
                            ->downloadable(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('entity_type')
                    ->label('Jenis Entitas')
                    ->formatStateUsing(fn($state) => match ($state) {
                        \App\Models\Schools\Building::class => 'Bangunan',
                        \App\Models\Schools\Land::class => 'Tanah',
                        default => $state
                    })
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('entity.name')
                    ->label('Nama Entitas')
                    ->searchable()
                    ->url(fn(InfraCondition $record) => match ($record->entity_type) {
                        \App\Models\Schools\Building::class => BuildingResource::getUrl('edit', ['record' => $record->entity_id]),
                        \App\Models\Schools\Land::class => LandResource::getUrl('edit', ['record' => $record->entity_id]),
                        default => null
                    }),

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

                Tables\Filters\SelectFilter::make('entity_type')
                    ->label('Jenis Entitas')
                    ->options([
                        \App\Models\Schools\Building::class => 'Bangunan',
                        \App\Models\Schools\Land::class => 'Tanah',
                    ]),

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
                    ->action(function (InfraCondition $record, array $data) {
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
                    Tables\Actions\DeleteBulkAction::make(),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInfraConditions::route('/'),
            'create' => Pages\CreateInfraCondition::route('/create'),
            'edit' => Pages\EditInfraCondition::route('/{record}/edit'),
        ];
    }
}
