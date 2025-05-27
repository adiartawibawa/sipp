<?php

namespace App\Filament\Clusters\Schools\Resources;

use App\Filament\Clusters\Schools;
use App\Filament\Clusters\Schools\Resources\OtherFacilityResource\Pages;
use App\Filament\Clusters\Schools\Resources\OtherFacilityResource\RelationManagers;
use App\Models\Schools\OtherFacility;
use App\Models\Schools\School;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class OtherFacilityResource extends Resource
{
    protected static ?string $model = OtherFacility::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $cluster = Schools::class;

    protected static ?string $modelLabel = 'Fasilitas Lainnya';

    protected static ?string $pluralModelLabel = 'Fasilitas Lainnya';

    protected static ?string $navigationLabel = 'Fasilitas Lain';

    protected static ?string $navigationGroup = 'Fasilitas Sekolah';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Dasar')
                    ->schema([
                        Forms\Components\Select::make('school_id')
                            ->label('Sekolah')
                            ->relationship('school', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->native(false),

                        Forms\Components\TextInput::make('category')
                            ->label('Kategori')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('name')
                            ->label('Nama Fasilitas')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $operation, $state, Forms\Set $set) {
                                if ($operation !== 'create') {
                                    return;
                                }

                                $set('code', Str::slug($state));
                            }),

                        Forms\Components\TextInput::make('code')
                            ->label('Kode Unik')
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->columnSpan(1),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Detail Fasilitas')
                    ->schema([
                        Forms\Components\TextInput::make('qty')
                            ->label('Jumlah')
                            ->required()
                            ->numeric()
                            ->default(1)
                            ->minValue(1),

                        Forms\Components\Textarea::make('specs')
                            ->label('Spesifikasi')
                            ->maxLength(65535)
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('value')
                            ->label('Nilai (Rp)')
                            ->numeric()
                            ->step(1000)
                            ->prefix('Rp'),

                        Forms\Components\TextInput::make('acq_year')
                            ->label('Tahun Perolehan')
                            ->numeric()
                            ->minValue(1900)
                            ->maxValue(now()->year),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Kode')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Fasilitas')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('category')
                    ->label('Kategori')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('school.name')
                    ->label('Sekolah')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('qty')
                    ->label('Jumlah')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('value')
                    ->label('Nilai')
                    ->money('IDR')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('acq_year')
                    ->label('Tahun')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('age')
                    ->label('Usia (thn)')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('conditions_count')
                    ->label('Kondisi')
                    ->counts('conditions')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('school')
                    ->relationship('school', 'name')
                    ->searchable()
                    ->preload()
                    ->native(false),

                Tables\Filters\TernaryFilter::make('has_conditions')
                    ->label('Memiliki Kondisi')
                    ->queries(
                        true: fn(Builder $query) => $query->has('conditions'),
                        false: fn(Builder $query) => $query->doesntHave('conditions'),
                    ),

                Tables\Filters\Filter::make('high_value')
                    ->label('Nilai Tinggi (>50jt)')
                    ->query(fn(Builder $query) => $query->where('value', '>', 50000000)),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ConditionsRelationManager::class,
            RelationManagers\AcquisitionsRelationManager::class,
            RelationManagers\RelocationsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOtherFacilities::route('/'),
            'create' => Pages\CreateOtherFacility::route('/create'),
            'edit' => Pages\EditOtherFacility::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['school', 'conditions'])
            ->withCount(['conditions', 'acquisitions', 'relocations']);
    }
}
