<?php

namespace App\Filament\Clusters\Schools\Resources;

use App\Filament\Clusters\Schools;
use App\Filament\Clusters\Schools\Resources\InfraCategoryResource\Pages;
use App\Filament\Clusters\Schools\Resources\InfraCategoryResource\RelationManagers;
use App\Models\Schools\InfraCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

class InfraCategoryResource extends Resource
{
    protected static ?string $model = InfraCategory::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $cluster = Schools::class;

    protected static ?string $modelLabel = 'Kategori Infrastruktur';

    protected static ?string $pluralModelLabel = 'Kategori Infrastruktur';

    protected static ?string $navigationGroup = 'Manajemen Infrastruktur';

    protected static ?int $navigationSort = 20;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Kategori')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Kategori')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $operation, $state, Forms\Set $set) {
                                if ($operation !== 'create') {
                                    return;
                                }

                                $set('slug', Str::slug($state));
                            }),

                        Forms\Components\TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),

                        Forms\Components\Textarea::make('desc')
                            ->label('Deskripsi')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('lands_count')
                    ->label('Jumlah Tanah')
                    ->counts('lands')
                    ->sortable(),

                Tables\Columns\TextColumn::make('buildings_count')
                    ->label('Jumlah Bangunan')
                    ->counts('buildings')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Diperbarui Pada')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('has_lands')
                    ->label('Memiliki Tanah')
                    ->options([
                        'yes' => 'Ya',
                        'no' => 'Tidak',
                    ])
                    ->query(function (Builder $query, array $data) {
                        if ($data['value'] === 'yes') {
                            $query->has('lands');
                        } elseif ($data['value'] === 'no') {
                            $query->doesntHave('lands');
                        }
                    }),

                Tables\Filters\SelectFilter::make('has_buildings')
                    ->label('Memiliki Bangunan')
                    ->options([
                        'yes' => 'Ya',
                        'no' => 'Tidak',
                    ])
                    ->query(function (Builder $query, array $data) {
                        if ($data['value'] === 'yes') {
                            $query->has('buildings');
                        } elseif ($data['value'] === 'no') {
                            $query->doesntHave('buildings');
                        }
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->before(function (InfraCategory $record) {
                        if ($record->lands()->exists() || $record->buildings()->exists()) {
                            throw new \Exception('Tidak dapat menghapus kategori yang masih memiliki tanah atau bangunan terkait.');
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->before(function (Collection $records) {
                            $hasRelations = $records->filter(function ($record) {
                                return $record->lands()->exists() || $record->buildings()->exists();
                            });

                            if ($hasRelations->isNotEmpty()) {
                                throw new \Exception('Beberapa kategori memiliki tanah atau bangunan terkait dan tidak dapat dihapus.');
                            }
                        }),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\LandsRelationManager::class,
            RelationManagers\BuildingsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInfraCategories::route('/'),
            'create' => Pages\CreateInfraCategory::route('/create'),
            'edit' => Pages\EditInfraCategory::route('/{record}/edit'),
        ];
    }
}
