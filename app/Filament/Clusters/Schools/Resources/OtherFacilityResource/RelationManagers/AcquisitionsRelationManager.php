<?php

namespace App\Filament\Clusters\Schools\Resources\OtherFacilityResource\RelationManagers;

use App\Models\Schools\InfraAcquisition;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AcquisitionsRelationManager extends RelationManager
{
    protected static string $relationship = 'acquisitions';

    protected static ?string $title = 'Riwayat Perolehan';

    protected static ?string $modelLabel = 'Perolehan';

    protected static ?string $pluralModelLabel = 'Riwayat Perolehan';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('acquisition_type')
                    ->label('Jenis Perolehan')
                    ->options([
                        'pembelian' => 'Pembelian',
                        'hibah' => 'Hibah',
                        'bantuan' => 'Bantuan',
                        'produksi_sendiri' => 'Produksi Sendiri',
                    ])
                    ->required()
                    ->native(false),

                Forms\Components\DatePicker::make('date')
                    ->label('Tanggal Perolehan')
                    ->required()
                    ->default(now()),

                Forms\Components\TextInput::make('source')
                    ->label('Sumber')
                    ->maxLength(255)
                    ->required(),

                Forms\Components\TextInput::make('cost')
                    ->label('Biaya (Rp)')
                    ->numeric()
                    ->prefix('Rp'),

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
                Tables\Columns\TextColumn::make('acquisition_type')
                    ->label('Jenis')
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'pembelian' => 'Pembelian',
                        'hibah' => 'Hibah',
                        'bantuan' => 'Bantuan',
                        'produksi_sendiri' => 'Produksi Sendiri',
                        default => $state,
                    })
                    ->badge(),

                Tables\Columns\TextColumn::make('date')
                    ->label('Tanggal')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('source')
                    ->label('Sumber')
                    ->searchable(),

                Tables\Columns\TextColumn::make('cost')
                    ->label('Biaya')
                    ->money('IDR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('notes')
                    ->label('Catatan')
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('acquisition_type')
                    ->label('Jenis Perolehan')
                    ->options([
                        'pembelian' => 'Pembelian',
                        'hibah' => 'Hibah',
                        'bantuan' => 'Bantuan',
                        'produksi_sendiri' => 'Produksi Sendiri',
                    ])
                    ->native(false),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Tambah Riwayat'),
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
            ->defaultSort('date', 'desc');
    }
}
