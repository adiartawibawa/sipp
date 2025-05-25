<?php

namespace App\Filament\Clusters\Administrasi\Resources\DistrictResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class VillagesRelationManager extends RelationManager
{
    protected static string $relationship = 'villages';

    protected static ?string $title = 'Daftar Desa/Kelurahan';

    protected static ?string $modelLabel = 'Desa/Kelurahan';

    protected static ?string $pluralModelLabel = 'Daftar Desa/Kelurahan';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Identitas')
                    ->schema([
                        Forms\Components\TextInput::make('id')
                            ->label('Kode')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),

                        Forms\Components\TextInput::make('name')
                            ->label('Nama Desa/Kelurahan')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn($state, $set) => $set('slug', Str::slug($state))),

                        Forms\Components\TextInput::make('slug')
                            ->label('Slug')
                            ->maxLength(255)
                            ->disabled()
                            ->dehydrated()
                            ->unique(ignoreRecord: true),
                    ])->columns(2),

                Forms\Components\Section::make('Klasifikasi')
                    ->schema([
                        Forms\Components\Select::make('type')
                            ->label('Jenis')
                            ->options([
                                'desa' => 'Desa',
                                'kelurahan' => 'Kelurahan'
                            ])
                            ->required(),

                        Forms\Components\TextInput::make('postal_code')
                            ->label('Kode Pos')
                            ->numeric()
                            ->nullable(),
                    ])->columns(2)
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('Kode')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('type')
                    ->label('Jenis')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'desa' => 'info',
                        'kelurahan' => 'success',
                    }),

                Tables\Columns\TextColumn::make('postal_code')
                    ->label('Kode Pos')
                    ->searchable(),

                Tables\Columns\TextColumn::make('slug')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'desa' => 'Desa',
                        'kelurahan' => 'Kelurahan'
                    ])
                    ->label('Jenis Wilayah'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Tambah Desa/Kelurahan'),
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
}
