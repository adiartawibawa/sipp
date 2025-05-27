<?php

namespace App\Filament\Clusters\Schools\Resources\SchoolResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class LandsRelationManager extends RelationManager
{
    protected static string $relationship = 'lands';

    protected static ?string $title = 'Tanah Sekolah';

    protected static ?string $modelLabel = 'Tanah';

    protected static ?string $pluralModelLabel = 'Daftar Tanah';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('certificate_no')
                    ->label('Nomor Sertifikat')
                    ->required()
                    ->maxLength(100),
                Forms\Components\TextInput::make('area')
                    ->label('Luas (m²)')
                    ->numeric()
                    ->required()
                    ->step(0.01),
                Forms\Components\Select::make('ownership_status')
                    ->label('Status Kepemilikan')
                    ->options([
                        'milik_sendiri' => 'Milik Sendiri',
                        'sewa' => 'Sewa',
                        'pinjam_pakai' => 'Pinjam Pakai',
                        'lainnya' => 'Lainnya',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('certificate_year')
                    ->label('Tahun Sertifikat')
                    ->numeric()
                    ->minValue(1900)
                    ->maxValue(now()->year),
                Forms\Components\Textarea::make('address')
                    ->label('Alamat Tanah')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('description')
                    ->label('Keterangan')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('certificate_no')
            ->columns([
                Tables\Columns\TextColumn::make('certificate_no')
                    ->label('No. Sertifikat')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('area')
                    ->label('Luas (m²)')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('ownership_status')
                    ->label('Status Kepemilikan')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'milik_sendiri' => 'success',
                        'sewa' => 'warning',
                        'pinjam_pakai' => 'info',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('certificate_year')
                    ->label('Tahun Sertifikat')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('ownership_status')
                    ->label('Status Kepemilikan')
                    ->options([
                        'milik_sendiri' => 'Milik Sendiri',
                        'sewa' => 'Sewa',
                        'pinjam_pakai' => 'Pinjam Pakai',
                        'lainnya' => 'Lainnya',
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
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }
}
