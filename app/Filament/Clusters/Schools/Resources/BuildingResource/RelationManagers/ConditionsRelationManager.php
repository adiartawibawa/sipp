<?php

namespace App\Filament\Clusters\Schools\Resources\BuildingResource\RelationManagers;

use App\Models\Schools\InfraCondition;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ConditionsRelationManager extends RelationManager
{
    protected static string $relationship = 'conditions';

    protected static ?string $modelLabel = 'Kondisi';

    protected static ?string $pluralModelLabel = 'Kondisi Bangunan';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('condition_type_id')
                    ->label('Jenis Kondisi')
                    ->relationship('type', 'name')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->native(false),

                Forms\Components\Select::make('condition_status_id')
                    ->label('Status Kondisi')
                    ->relationship('status', 'name')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->native(false),

                Forms\Components\DatePicker::make('checked_at')
                    ->label('Tanggal Pengecekan')
                    ->required()
                    ->default(now()),

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
                    ->label('Jenis Kondisi')
                    ->searchable(),

                Tables\Columns\TextColumn::make('status.name')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Baik' => 'success',
                        'Rusak Ringan' => 'warning',
                        'Rusak Berat' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('checked_at')
                    ->label('Tanggal Pengecekan')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('checked_by.name')
                    ->label('Dicek Oleh')
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->relationship('status', 'name')
                    ->label('Filter Status Kondisi')
                    ->searchable()
                    ->preload(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['checked_by'] = auth()->id();
                        return $data;
                    }),
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
            ->with(['type', 'status', 'checkedBy'])
            ->latest('checked_at');
    }
}
