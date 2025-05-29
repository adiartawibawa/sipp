<?php

namespace App\Filament\Clusters\Schools\Resources\InfraAcquisitionResource\RelationManagers;

use App\Models\Schools\InfraAcquisition;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EntityInfraAcquisitionsRelationManager extends RelationManager
{
    protected static string $relationship = 'infraAcquisitions';

    protected static ?string $title = 'Riwayat Perolehan';

    protected static ?string $modelLabel = 'Perolehan';

    protected static ?string $pluralModelLabel = 'Daftar Perolehan';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('source')
                    ->label('Sumber Perolehan')
                    ->required()
                    ->options([
                        'purchase' => 'Pembelian',
                        'donation' => 'Donasi',
                        'grant' => 'Hibah',
                        'government' => 'Pemerintah',
                        'other' => 'Lainnya',
                    ]),

                Forms\Components\TextInput::make('amount')
                    ->label('Jumlah/Nilai')
                    ->numeric()
                    ->step(0.01)
                    ->suffix('IDR'),

                Forms\Components\TextInput::make('year')
                    ->label('Tahun Perolehan')
                    ->required()
                    ->numeric()
                    ->minValue(2000)
                    ->maxValue(now()->year),

                Forms\Components\Select::make('method')
                    ->label('Metode Perolehan')
                    ->options([
                        'cash' => 'Tunai',
                        'credit' => 'Kredit',
                        'installment' => 'Cicilan',
                        'barter' => 'Tukar Guling',
                    ]),

                Forms\Components\Textarea::make('notes')
                    ->label('Catatan')
                    ->columnSpanFull()
                    ->maxLength(500),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('source')
                    ->label('Sumber')
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'purchase' => 'Pembelian',
                        'donation' => 'Donasi',
                        'grant' => 'Hibah',
                        'government' => 'Pemerintah',
                        'other' => 'Lainnya',
                        default => $state,
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Nilai')
                    ->numeric(
                        decimalPlaces: 2,
                        decimalSeparator: ',',
                        thousandsSeparator: '.',
                    )
                    ->money('IDR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('year')
                    ->label('Tahun')
                    ->sortable(),

                Tables\Columns\TextColumn::make('method')
                    ->label('Metode')
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'cash' => 'Tunai',
                        'credit' => 'Kredit',
                        'installment' => 'Cicilan',
                        'barter' => 'Tukar Guling',
                        default => $state,
                    })
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Input')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('year')
                    ->label('Tahun')
                    ->options(fn(): array => InfraAcquisition::query()
                        ->where('entity_id', $this->getOwnerRecord()->id)
                        ->where('entity_type', $this->getOwnerRecord()::class)
                        ->pluck('year', 'year')
                        ->unique()
                        ->sort()
                        ->toArray()),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['entity_id'] = $this->getOwnerRecord()->id;
                        $data['entity_type'] = $this->getOwnerRecord()::class;
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
            ]);
    }
}
