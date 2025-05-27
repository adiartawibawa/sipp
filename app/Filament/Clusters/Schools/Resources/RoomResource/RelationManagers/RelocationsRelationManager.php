<?php

namespace App\Filament\Clusters\Schools\Resources\RoomResource\RelationManagers;

use App\Models\Schools\InfraRelocation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class RelocationsRelationManager extends RelationManager
{
    protected static string $relationship = 'relocations';

    protected static ?string $title = 'Riwayat Pemindahan';

    protected static ?string $modelLabel = 'Pemindahan';

    protected static ?string $pluralModelLabel = 'Riwayat Pemindahan';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('from')
                    ->label('Dari Lokasi')
                    ->maxLength(100),
                Forms\Components\TextInput::make('to')
                    ->required()
                    ->maxLength(100)
                    ->label('Ke Lokasi'),
                Forms\Components\DatePicker::make('moved_at')
                    ->required()
                    ->label('Tanggal Pemindahan')
                    ->default(now()),
                Forms\Components\Textarea::make('notes')
                    ->label('Catatan')
                    ->maxLength(500)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('to')
            ->columns([
                Tables\Columns\TextColumn::make('from')
                    ->label('Dari')
                    ->searchable(),
                Tables\Columns\TextColumn::make('to')
                    ->label('Ke')
                    ->searchable(),
                Tables\Columns\TextColumn::make('moved_at')
                    ->label('Tanggal')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('notes')
                    ->label('Catatan')
                    ->limit(30)
                    ->tooltip(fn(InfraRelocation $record) => $record->notes)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\Filter::make('moved_at')
                    ->form([
                        Forms\Components\DatePicker::make('moved_from')
                            ->label('Dari Tanggal'),
                        Forms\Components\DatePicker::make('moved_until')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['moved_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('moved_at', '>=', $date)
                            )
                            ->when(
                                $data['moved_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('moved_at', '<=', $date)
                            );
                    }),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Tambah Riwayat Pemindahan'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('moved_at', 'desc');
    }
}
