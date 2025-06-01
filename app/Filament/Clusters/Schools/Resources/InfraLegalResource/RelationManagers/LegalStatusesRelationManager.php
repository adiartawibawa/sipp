<?php

namespace App\Filament\Clusters\Schools\Resources\InfraLegalResource\RelationManagers;

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

    protected static ?string $pluralModelLabel = 'Status Hukum';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('status')
                    ->label('Status Hukum')
                    ->required()
                    ->options([
                        'shm' => 'Sertifikat Hak Milik (SHM)',
                        'shgb' => 'Sertifikat Hak Guna Bangunan (SHGB)',
                        'hpl' => 'Hak Pengelolaan Lainnya (HPL)',
                        'sewa' => 'Sewa',
                        'pinjam_pakai' => 'Pinjam Pakai',
                        'lainnya' => 'Lainnya',
                    ])
                    ->native(false),

                Forms\Components\TextInput::make('doc_no')
                    ->label('Nomor Dokumen')
                    ->maxLength(100),

                Forms\Components\DatePicker::make('doc_date')
                    ->label('Tanggal Dokumen'),

                Forms\Components\Textarea::make('notes')
                    ->label('Catatan')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('status')
                    ->label('Status Hukum')
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'shm' => 'SHM',
                        'shgb' => 'SHGB',
                        'hpl' => 'HPL',
                        'sewa' => 'Sewa',
                        'pinjam_pakai' => 'Pinjam Pakai',
                        'lainnya' => 'Lainnya',
                        default => $state,
                    })
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('doc_no')
                    ->label('No. Dokumen')
                    ->searchable(),

                Tables\Columns\TextColumn::make('doc_date')
                    ->label('Tgl. Dokumen')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status Hukum')
                    ->options([
                        'shm' => 'SHM',
                        'shgb' => 'SHGB',
                        'hpl' => 'HPL',
                        'sewa' => 'Sewa',
                        'pinjam_pakai' => 'Pinjam Pakai',
                        'lainnya' => 'Lainnya',
                    ]),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['entity_type'] = $this->getOwnerRecord()::class;
                        $data['entity_id'] = $this->getOwnerRecord()->id;
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

    protected function getTableQuery(): Builder
    {
        return parent::getTableQuery()
            ->latest();
    }
}
