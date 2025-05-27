<?php

namespace App\Filament\Clusters\Schools\Resources\LandResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RelocationsRelationManager extends RelationManager
{
    protected static string $relationship = 'relocations';

    protected static ?string $title = 'Riwayat Pemindahan';

    protected static ?string $modelLabel = 'Pemindahan';

    protected static ?string $pluralModelLabel = 'Riwayat Pemindahan Tanah';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('relocation_date')
                    ->label('Tanggal Pemindahan')
                    ->required()
                    ->default(now()),

                Forms\Components\TextInput::make('from_location')
                    ->label('Dari Lokasi')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('to_location')
                    ->label('Ke Lokasi')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('reason')
                    ->label('Alasan Pemindahan')
                    ->required()
                    ->maxLength(255),

                Forms\Components\Textarea::make('notes')
                    ->label('Catatan')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('relocation_date')
            ->columns([
                Tables\Columns\TextColumn::make('relocation_date')
                    ->label('Tanggal')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('from_location')
                    ->label('Dari')
                    ->searchable(),

                Tables\Columns\TextColumn::make('to_location')
                    ->label('Ke')
                    ->searchable(),

                Tables\Columns\TextColumn::make('reason')
                    ->label('Alasan')
                    ->searchable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('relocation_date')
                    ->form([
                        Forms\Components\DatePicker::make('from_date')
                            ->label('Dari Tanggal'),
                        Forms\Components\DatePicker::make('to_date')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from_date'],
                                fn(Builder $query, $date): Builder => $query->whereDate('relocation_date', '>=', $date),
                            )
                            ->when(
                                $data['to_date'],
                                fn(Builder $query, $date): Builder => $query->whereDate('relocation_date', '<=', $date),
                            );
                    }),
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
