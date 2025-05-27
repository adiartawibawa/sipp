<?php

namespace App\Filament\Clusters\Schools\Resources\LandResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AcquisitionsRelationManager extends RelationManager
{
    protected static string $relationship = 'acquisitions';

    protected static ?string $title = 'Riwayat Perolehan';

    protected static ?string $modelLabel = 'Perolehan';

    protected static ?string $pluralModelLabel = 'Riwayat Perolehan Tanah';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('acquisition_type')
                    ->label('Jenis Perolehan')
                    ->required()
                    ->options([
                        'purchase' => 'Pembelian',
                        'grant' => 'Hibah',
                        'exchange' => 'Tukar Guling',
                        'confiscation' => 'Sitaan',
                        'other' => 'Lainnya',
                    ])
                    ->native(false),

                Forms\Components\DatePicker::make('acquisition_date')
                    ->label('Tanggal Perolehan')
                    ->required()
                    ->default(now()),

                Forms\Components\TextInput::make('source')
                    ->label('Sumber Perolehan')
                    ->maxLength(255),

                Forms\Components\TextInput::make('cost')
                    ->label('Biaya Perolehan')
                    ->numeric()
                    ->prefix('Rp'),

                Forms\Components\Textarea::make('notes')
                    ->label('Catatan')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('acquisition_type')
            ->columns([
                Tables\Columns\TextColumn::make('acquisition_type')
                    ->label('Jenis')
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'purchase' => 'Pembelian',
                        'grant' => 'Hibah',
                        'exchange' => 'Tukar Guling',
                        'confiscation' => 'Sitaan',
                        'other' => 'Lainnya',
                        default => $state,
                    })
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'purchase' => 'info',
                        'grant' => 'success',
                        'exchange' => 'warning',
                        'confiscation' => 'danger',
                        'other' => 'gray',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('acquisition_date')
                    ->label('Tanggal')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('source')
                    ->label('Sumber')
                    ->searchable(),

                Tables\Columns\TextColumn::make('cost')
                    ->label('Biaya')
                    ->numeric(
                        decimalPlaces: 0,
                        decimalSeparator: '.',
                        thousandsSeparator: ',',
                    )
                    ->prefix('Rp '),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('acquisition_type')
                    ->label('Jenis Perolehan')
                    ->options([
                        'purchase' => 'Pembelian',
                        'grant' => 'Hibah',
                        'exchange' => 'Tukar Guling',
                        'confiscation' => 'Sitaan',
                        'other' => 'Lainnya',
                    ]),
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
