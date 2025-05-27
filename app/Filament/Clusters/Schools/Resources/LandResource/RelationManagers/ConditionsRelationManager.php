<?php

namespace App\Filament\Clusters\Schools\Resources\LandResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ConditionsRelationManager extends RelationManager
{
    protected static string $relationship = 'conditions';

    protected static ?string $title = 'Kondisi Tanah';

    protected static ?string $modelLabel = 'Kondisi';

    protected static ?string $pluralModelLabel = 'Kondisi Tanah';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('condition_date')
                    ->label('Tanggal Kondisi')
                    ->required()
                    ->default(now()),

                Forms\Components\Select::make('condition_status')
                    ->label('Status Kondisi')
                    ->required()
                    ->options([
                        'good' => 'Baik',
                        'moderate' => 'Sedang',
                        'bad' => 'Rusak',
                        'severe' => 'Rusak Berat',
                    ])
                    ->native(false),

                Forms\Components\Textarea::make('description')
                    ->label('Deskripsi Kondisi')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('condition_date')
            ->columns([
                Tables\Columns\TextColumn::make('condition_date')
                    ->label('Tanggal')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('condition_status')
                    ->label('Status')
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'good' => 'Baik',
                        'moderate' => 'Sedang',
                        'bad' => 'Rusak',
                        'severe' => 'Rusak Berat',
                        default => $state,
                    })
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'good' => 'success',
                        'moderate' => 'warning',
                        'bad' => 'danger',
                        'severe' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('description')
                    ->label('Deskripsi')
                    ->limit(50)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= $column->getCharacterLimit()) {
                            return null;
                        }
                        return $state;
                    }),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
