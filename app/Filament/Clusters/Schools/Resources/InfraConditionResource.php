<?php

namespace App\Filament\Clusters\Schools\Resources;

use App\Filament\Clusters\Schools;
use App\Filament\Clusters\Schools\Resources\InfraConditionResource\Pages;
use App\Filament\Clusters\Schools\Resources\InfraConditionResource\RelationManagers;
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

    protected static ?string $navigationLabel = 'Kondisi Infra';

    protected static ?string $navigationGroup = 'Manajemen Infrastruktur';

    protected static ?int $navigationSort = 22;

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
                            ->required(),

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
                            ->required(),

                        Forms\Components\DatePicker::make('checked_at')
                            ->label('Tanggal Pengecekan')
                            ->required()
                            ->default(now()),

                        Forms\Components\Textarea::make('notes')
                            ->label('Catatan')
                            ->columnSpanFull(),
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
                    ->sortable(),

                Tables\Columns\TextColumn::make('entity.name')
                    ->label('Nama Entitas')
                    ->searchable(),

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
                    ->sortable(),
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
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            ]);
    }

    public static function getRelations(): array
    {
        return [];
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
