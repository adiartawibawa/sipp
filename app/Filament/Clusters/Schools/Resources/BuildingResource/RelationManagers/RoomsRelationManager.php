<?php

namespace App\Filament\Clusters\Schools\Resources\BuildingResource\RelationManagers;

use App\Models\Schools\Room;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class RoomsRelationManager extends RelationManager
{
    protected static string $relationship = 'rooms';

    protected static ?string $modelLabel = 'Ruang';

    protected static ?string $pluralModelLabel = 'Data Ruangan';

    protected static ?string $title = 'Daftar Ruangan';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('code')
                    ->label('Kode Ruangan')
                    ->required()
                    ->maxLength(20)
                    ->unique(ignoreRecord: true),

                Forms\Components\TextInput::make('name')
                    ->label('Nama Ruangan')
                    ->required()
                    ->maxLength(100),

                Forms\Components\TextInput::make('length')
                    ->label('Panjang (m)')
                    ->numeric()
                    ->step(0.01)
                    ->maxValue(999.99),

                Forms\Components\TextInput::make('width')
                    ->label('Lebar (m)')
                    ->numeric()
                    ->step(0.01)
                    ->maxValue(999.99),

                Forms\Components\TextInput::make('area')
                    ->label('Luas (m²)')
                    ->numeric()
                    ->step(0.01)
                    ->maxValue(9999.99)
                    ->helperText('Luas otomatis dihitung dari panjang x lebar jika kosong'),

                Forms\Components\TextInput::make('capacity')
                    ->label('Kapasitas')
                    ->integer()
                    ->minValue(1),

                Forms\Components\Select::make('room_type_id')
                    ->label('Tipe Ruangan')
                    ->relationship('type', 'name')
                    ->searchable()
                    ->preload()
                    ->native(false),

                Forms\Components\Textarea::make('notes')
                    ->label('Catatan')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Kode')
                    ->searchable(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Ruangan')
                    ->searchable(),

                Tables\Columns\TextColumn::make('type.name')
                    ->label('Tipe')
                    ->searchable(),

                Tables\Columns\TextColumn::make('area')
                    ->label('Luas (m²)')
                    ->numeric(decimalPlaces: 2)
                    ->sortable(),

                Tables\Columns\TextColumn::make('capacity')
                    ->label('Kapasitas')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->relationship('type', 'name')
                    ->label('Filter Tipe Ruangan')
                    ->searchable()
                    ->preload(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['created_by'] = auth()->id();
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
            ->defaultSort('name');
    }

    protected function getTableQuery(): Builder
    {
        return parent::getTableQuery()
            ->with(['type'])
            ->orderBy('name');
    }
}
