<?php

namespace App\Filament\Clusters\Schools\Resources\RoomResource\RelationManagers;

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

    protected static ?string $title = 'Status Hukum';

    protected static ?string $modelLabel = 'Status Hukum';

    protected static ?string $pluralModelLabel = 'Daftar Status Hukum';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('status')
                    ->options([
                        'SHM' => 'Sertifikat Hak Milik (SHM)',
                        'HGB' => 'Hak Guna Bangunan (HGB)',
                        'HP' => 'Hak Pakai',
                        'Girik' => 'Girik',
                        'Other' => 'Lainnya',
                    ])
                    ->required()
                    ->label('Status Hukum'),
                Forms\Components\TextInput::make('doc_no')
                    ->label('Nomor Dokumen')
                    ->maxLength(50),
                Forms\Components\DatePicker::make('doc_date')
                    ->label('Tanggal Dokumen'),
                Forms\Components\Textarea::make('notes')
                    ->label('Catatan')
                    ->maxLength(500)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('status')
            ->columns([
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'SHM' => 'SHM',
                        'HGB' => 'HGB',
                        'HP' => 'Hak Pakai',
                        'Girik' => 'Girik',
                        default => 'Lainnya',
                    })
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'SHM' => 'success',
                        'HGB' => 'primary',
                        'HP' => 'info',
                        'Girik' => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('doc_no')
                    ->label('Nomor Dokumen')
                    ->searchable(),
                Tables\Columns\TextColumn::make('doc_date')
                    ->label('Tanggal Dokumen')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status Hukum')
                    ->options([
                        'SHM' => 'SHM',
                        'HGB' => 'HGB',
                        'HP' => 'Hak Pakai',
                        'Girik' => 'Girik',
                        'Other' => 'Lainnya',
                    ]),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Tambah Status Hukum'),
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
            ->defaultSort('doc_date', 'desc');
    }
}
