<?php

namespace App\Filament\Clusters\Schools\Resources\OtherFacilityResource\RelationManagers;

use App\Models\Schools\FacilityCondition;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ConditionsRelationManager extends RelationManager
{
    protected static string $relationship = 'conditions';

    protected static ?string $title = 'Kondisi Fasilitas';

    protected static ?string $modelLabel = 'Kondisi';

    protected static ?string $pluralModelLabel = 'Kondisi Fasilitas';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('condition')
                    ->label('Kondisi')
                    ->options([
                        'baik' => 'Baik',
                        'rusak_ringan' => 'Rusak Ringan',
                        'rusak_berat' => 'Rusak Berat',
                        'hilang' => 'Hilang',
                    ])
                    ->required()
                    ->native(false),

                Forms\Components\DatePicker::make('checked_at')
                    ->label('Tanggal Pemeriksaan')
                    ->required()
                    ->default(now()),

                Forms\Components\TextInput::make('checked_by')
                    ->label('Diperiksa Oleh')
                    ->maxLength(255)
                    ->required(),

                Forms\Components\Textarea::make('notes')
                    ->label('Catatan')
                    ->maxLength(65535)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('condition')
                    ->label('Kondisi')
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'baik' => 'Baik',
                        'rusak_ringan' => 'Rusak Ringan',
                        'rusak_berat' => 'Rusak Berat',
                        'hilang' => 'Hilang',
                        default => $state,
                    })
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'baik' => 'success',
                        'rusak_ringan' => 'warning',
                        'rusak_berat' => 'danger',
                        'hilang' => 'gray',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('checked_at')
                    ->label('Tanggal Pemeriksaan')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('checked_by')
                    ->label('Diperiksa Oleh')
                    ->searchable(),

                Tables\Columns\TextColumn::make('notes')
                    ->label('Catatan')
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('condition')
                    ->label('Kondisi')
                    ->options([
                        'baik' => 'Baik',
                        'rusak_ringan' => 'Rusak Ringan',
                        'rusak_berat' => 'Rusak Berat',
                        'hilang' => 'Hilang',
                    ])
                    ->native(false),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Tambah Kondisi'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('checked_at', 'desc');
    }

    protected function getTableQuery(): Builder
    {
        return parent::getTableQuery()
            ->withoutGlobalScopes();
    }
}
