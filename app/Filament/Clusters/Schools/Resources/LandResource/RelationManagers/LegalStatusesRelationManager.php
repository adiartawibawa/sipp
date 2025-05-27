<?php

namespace App\Filament\Clusters\Schools\Resources\LandResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LegalStatusesRelationManager extends RelationManager
{
    protected static string $relationship = 'legalStatuses';

    protected static ?string $title = 'Status Hukum';

    protected static ?string $modelLabel = 'Status Hukum';

    protected static ?string $pluralModelLabel = 'Status Hukum Tanah';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('status_type')
                    ->label('Jenis Status')
                    ->required()
                    ->options([
                        'certified' => 'Bersertifikat',
                        'unregistered' => 'Belum Terdaftar',
                        'dispute' => 'Sengketa',
                        'process' => 'Proses Pendaftaran',
                    ])
                    ->native(false),

                Forms\Components\DatePicker::make('start_date')
                    ->label('Tanggal Mulai')
                    ->required()
                    ->default(now()),

                Forms\Components\DatePicker::make('end_date')
                    ->label('Tanggal Berakhir')
                    ->afterOrEqual('start_date'),

                Forms\Components\Textarea::make('description')
                    ->label('Keterangan')
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('legal_document_no')
                    ->label('Nomor Dokumen Hukum')
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('status_type')
            ->columns([
                Tables\Columns\TextColumn::make('status_type')
                    ->label('Jenis Status')
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'certified' => 'Bersertifikat',
                        'unregistered' => 'Belum Terdaftar',
                        'dispute' => 'Sengketa',
                        'process' => 'Proses Pendaftaran',
                        default => $state,
                    })
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'certified' => 'success',
                        'unregistered' => 'warning',
                        'dispute' => 'danger',
                        'process' => 'info',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('start_date')
                    ->label('Mulai')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('end_date')
                    ->label('Berakhir')
                    ->date(),

                Tables\Columns\TextColumn::make('legal_document_no')
                    ->label('No. Dokumen')
                    ->searchable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status_type')
                    ->label('Status')
                    ->options([
                        'certified' => 'Bersertifikat',
                        'unregistered' => 'Belum Terdaftar',
                        'dispute' => 'Sengketa',
                        'process' => 'Proses Pendaftaran',
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
