<?php

namespace App\Filament\Clusters\Schools\Resources;

use App\Filament\Clusters\Schools;
use App\Models\Schools\InfraLegal;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InfraLegalResource extends Resource
{
    protected static ?string $model = InfraLegal::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Status Hukum Infrastruktur';

    protected static ?string $modelLabel = 'Status Hukum Infrastruktur';

    protected static ?string $pluralModelLabel = 'Status Hukum Infrastruktur';

    protected static ?string $cluster = Schools::class;

    protected static ?string $navigationGroup = 'Legal & Dokumen';

    protected static ?int $navigationSort = 30;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('entity_type')
                    ->label('Jenis Entitas')
                    ->required()
                    ->options([
                        'building' => 'Gedung',
                        'land' => 'Tanah',
                        'room' => 'Ruang',
                    ])
                    ->live()
                    ->afterStateUpdated(fn($state, Forms\Set $set) => $set('entity_id', null)),

                Forms\Components\Select::make('entity_id')
                    ->label('Entitas')
                    ->required()
                    ->options(function (Forms\Get $get) {
                        $entityType = $get('entity_type');

                        if (!$entityType) {
                            return [];
                        }

                        $modelClass = match ($entityType) {
                            'building' => \App\Models\Schools\Building::class,
                            'land' => \App\Models\Schools\Land::class,
                            'room' => \App\Models\Schools\Room::class,
                            default => null,
                        };

                        if (!$modelClass) {
                            return [];
                        }

                        return $modelClass::query()
                            ->orderBy('name')
                            ->get()
                            ->pluck('name', 'id');
                    })
                    ->searchable(),

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

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('entity_type')
                    ->label('Jenis Entitas')
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'building' => 'Gedung',
                        'land' => 'Tanah',
                        'room' => 'Ruang',
                        default => $state,
                    })
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('entity.name')
                    ->label('Nama Entitas')
                    ->sortable()
                    ->searchable(),

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
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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

                Tables\Filters\SelectFilter::make('entity_type')
                    ->label('Jenis Entitas')
                    ->options([
                        'building' => 'Gedung',
                        'land' => 'Tanah',
                        'room' => 'Ruang',
                    ]),
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

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['entity'])
            ->orderByDesc('created_at');
    }

    public static function getRelations(): array
    {
        return [
            // Relation manager akan ditambahkan di sini jika diperlukan
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Clusters\Schools\Resources\InfraLegalResource\Pages\ListInfraLegals::route('/'),
            'create' => \App\Filament\Clusters\Schools\Resources\InfraLegalResource\Pages\CreateInfraLegal::route('/create'),
            'edit' => \App\Filament\Clusters\Schools\Resources\InfraLegalResource\Pages\EditInfraLegal::route('/{record}/edit'),
        ];
    }
}
