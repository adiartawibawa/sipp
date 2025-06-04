<?php

namespace App\Filament\Clusters\Schools\Resources\SchoolResource\RelationManagers;

use App\Models\Schools\InfraCategory;
use App\Models\Schools\InfraCondition;
use App\Models\Schools\OtherFacility;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class OtherFacilitiesRelationManager extends RelationManager
{
    protected static string $relationship = 'otherFacilities';

    protected static ?string $title = 'Fasilitas Lainnya';

    protected static ?string $modelLabel = 'Fasilitas';

    protected static ?string $pluralModelLabel = 'Daftar Fasilitas';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nama Fasilitas')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (Set $set, ?string $state) {
                        $set('code', Str::slug($state));
                    }),
                Forms\Components\Select::make('infra_cat_id')
                    ->label('Kategori Fasilitas')
                    ->relationship(
                        name: 'category',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn(Builder $query) => $query->whereIn('type', ['other'])
                    )
                    ->searchable()
                    ->preload()
                    ->required()
                    ->live()
                    ->afterStateUpdated(function (Get $get, Set $set) {
                        // Reset code ketika kategori diubah
                        if ($get('infra_cat_id')) {
                            $category = InfraCategory::find($get('infra_cat_id'));
                            $count = OtherFacility::where('infra_cat_id', $category->id)->count();
                            $set('code', $category->code . '-' . ($count + 1));
                        }
                    })
                    ->getOptionLabelFromRecordUsing(fn(InfraCategory $record) => "{$record->code} - {$record->name}"),

                // Input Kode Fasilitas
                Forms\Components\TextInput::make('code')
                    ->label('Kode Fasilitas')
                    ->maxLength(50)
                    ->required()
                    ->default(function (Get $get, Set $set) {
                        if (!$get('infra_cat_id')) {
                            return null;
                        }

                        $category = InfraCategory::find($get('infra_cat_id'));
                        $count = OtherFacility::where('infra_cat_id', $category->id)->count();
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
                Forms\Components\TextInput::make('qty')
                    ->label('Jumlah')
                    ->numeric()
                    ->minValue(1)
                    ->default(1)
                    ->required(),
                Forms\Components\Textarea::make('specs')
                    ->label('Spesifikasi')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('value')
                    ->label('Nilai (Rp)')
                    ->numeric()
                    ->step(0.01)
                    ->prefix('Rp'),
                Forms\Components\TextInput::make('acq_year')
                    ->label('Tahun Perolehan')
                    ->numeric()
                    ->minValue(1900)
                    ->maxValue(now()->year),
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
                    ->label('Nama Fasilitas')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Kategori')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('qty')
                    ->label('Jumlah')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('value')
                    ->label('Nilai')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('acq_year')
                    ->label('Tahun')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('age')
                    ->label('Usia (thn)')
                    ->numeric()
                    ->sortable()
                    ->state(function (OtherFacility $record): ?int {
                        return $record->age;
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
                Tables\Filters\Filter::make('high_value')
                    ->label('Nilai Tinggi (> Rp10jt)')
                    ->query(fn(Builder $query): Builder => $query->where('value', '>', 10000000)),
                Tables\Filters\Filter::make('old_facilities')
                    ->label('Fasilitas Tua (>10thn)')
                    ->query(fn(Builder $query): Builder => $query->where('acq_year', '<', now()->year - 10)),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                    Tables\Actions\Action::make('manage_conditions')
                        ->label('Kondisi')
                        ->icon('heroicon-m-wrench-screwdriver')
                        ->modalHeading('Kelola Kondisi Bangunan')
                        ->action(function (OtherFacility $record, array $data): void {
                            $record->conditions()->create([
                                'condition' => $data['condition'],
                                'percentage' => $data['percentage'],
                                'notes' => $data['notes'],
                                'checked_at' => $data['checked_at'],
                            ]);
                        })
                        ->form(fn(OtherFacility $record) => [
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
                            Forms\Components\View::make('filament.clusters.schools.resources.school.modals.other-facil-conditions-table')
                                ->columnSpanFull()
                                ->viewData(['other' => $record]),
                        ])
                        ->modalWidth('4xl'),
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
