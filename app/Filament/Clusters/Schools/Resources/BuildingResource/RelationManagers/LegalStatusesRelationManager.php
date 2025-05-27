<?php

namespace App\Filament\Clusters\Schools\Resources\BuildingResource\RelationManagers;

use App\Models\Schools\InfraLegal;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class LegalStatusesRelationManager extends RelationManager
{
    protected static string $relationship = 'legalStatuses';

    protected static ?string $modelLabel = 'Status Hukum';

    protected static ?string $pluralModelLabel = 'Status Hukum Bangunan';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('legal_type_id')
                    ->label('Jenis Status Hukum')
                    ->relationship('type', 'name')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->native(false),

                Forms\Components\TextInput::make('number')
                    ->label('Nomor Dokumen')
                    ->required()
                    ->maxLength(50),

                Forms\Components\DatePicker::make('issued_at')
                    ->label('Tanggal Dikeluarkan')
                    ->required(),

                Forms\Components\DatePicker::make('expired_at')
                    ->label('Tanggal Kadaluarsa')
                    ->after('issued_at'),

                Forms\Components\Textarea::make('notes')
                    ->label('Catatan')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('type.name')
            ->columns([
                Tables\Columns\TextColumn::make('type.name')
                    ->label('Jenis Status')
                    ->searchable(),

                Tables\Columns\TextColumn::make('number')
                    ->label('Nomor Dokumen')
                    ->searchable(),

                Tables\Columns\TextColumn::make('issued_at')
                    ->label('Tanggal Dikeluarkan')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('expired_at')
                    ->label('Kadaluarsa')
                    ->date()
                    ->sortable()
                    ->color(fn($record) => $record->expired_at && $record->expired_at->isPast() ? 'danger' : null),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dicatat Pada')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->relationship('type', 'name')
                    ->label('Filter Jenis Status')
                    ->searchable()
                    ->preload(),

                Tables\Filters\Filter::make('expired')
                    ->label('Status Kadaluarsa')
                    ->query(fn(Builder $query): Builder => $query->whereNotNull('expired_at')->where('expired_at', '<', now())),
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
            ])
            ->defaultSort('issued_at', 'desc');
    }

    protected function getTableQuery(): Builder
    {
        return parent::getTableQuery()
            ->with(['type'])
            ->latest('issued_at');
    }
}
