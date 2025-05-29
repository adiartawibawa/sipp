<?php

namespace App\Filament\Clusters\Schools\Resources;

use App\Filament\Clusters\Schools;
use App\Filament\Clusters\Schools\Resources\InfraDocumentResource\Pages\CreateInfraDocument;
use App\Filament\Clusters\Schools\Resources\InfraDocumentResource\Pages\EditInfraDocument;
use App\Filament\Clusters\Schools\Resources\InfraDocumentResource\Pages\ListInfraDocuments;
use App\Models\Schools\InfraDocument;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class InfraDocumentResource extends Resource
{
    protected static ?string $model = InfraDocument::class;

    protected static ?string $navigationIcon = 'heroicon-o-document';

    protected static ?string $navigationLabel = 'Dokumen Infrastruktur';

    protected static ?string $modelLabel = 'Dokumen';

    protected static ?string $pluralModelLabel = 'Dokumen Infrastruktur';

    protected static ?string $navigationGroup = 'Legal & Dokumen';

    protected static ?int $navigationSort = 30;

    protected static ?string $cluster = Schools::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Dokumen')
                    ->schema([
                        Forms\Components\Select::make('entity_type')
                            ->label('Jenis Entitas')
                            ->required()
                            ->live()
                            ->options([
                                'building' => 'Gedung',
                                'land' => 'Tanah',
                                'room' => 'Ruang',
                            ])
                            ->afterStateUpdated(fn($set) => $set('entity_id', null)),

                        Forms\Components\Select::make('entity_id')
                            ->label('Entitas Terkait')
                            ->required()
                            ->options(function (Forms\Get $get) {
                                $type = $get('entity_type');
                                if (!$type) return [];

                                return match ($type) {
                                    'building' => \App\Models\Schools\Building::pluck('name', 'id'),
                                    'land' => \App\Models\Schools\Land::pluck('name', 'id'),
                                    'room' => \App\Models\Schools\Room::pluck('name', 'id'),
                                    default => [],
                                };
                            })
                            ->searchable(),

                        Forms\Components\TextInput::make('name')
                            ->label('Nama Dokumen')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, $set) {
                                $set('name', Str::title($state));
                            }),
                    ])->columns(2),

                Forms\Components\Section::make('Upload Dokumen')
                    ->schema([
                        Forms\Components\FileUpload::make('path')
                            ->label('File Dokumen')
                            ->required()
                            ->directory('infra-documents')
                            ->preserveFilenames()
                            ->maxSize(10240) // 10MB
                            ->acceptedFileTypes([
                                'application/pdf',
                                'image/*',
                                'application/msword',
                                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                            ])
                            ->downloadable()
                            ->openable()
                            ->previewable(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('entity_type')
                    ->label('Jenis Entitas')
                    ->formatStateUsing(fn($state) => match ($state) {
                        'building' => 'Gedung',
                        'land' => 'Tanah',
                        'room' => 'Ruang',
                        default => $state,
                    })
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('entity.name')
                    ->label('Nama Entitas')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Dokumen')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('extension')
                    ->label('Format')
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Upload Pada')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('entity_type')
                    ->label('Filter Jenis Entitas')
                    ->options([
                        'building' => 'Gedung',
                        'land' => 'Tanah',
                        'room' => 'Ruang',
                    ]),

                Tables\Filters\Filter::make('upload_date')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('Dari Tanggal'),
                        Forms\Components\DatePicker::make('until')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn($query) => $query->whereDate('created_at', '>=', $data['from'])
                            )
                            ->when(
                                $data['until'],
                                fn($query) => $query->whereDate('created_at', '<=', $data['until'])
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('download')
                    ->label('Unduh')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn($record) => $record->url)
                    ->openUrlInNewTab(),

                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    // \App\Filament\Clusters\Schools\Resources\InfraDocumentResource\Actions\BulkDownloadAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListInfraDocuments::route('/'),
            'create' => CreateInfraDocument::route('/create'),
            'edit' => EditInfraDocument::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['entity'])
            ->orderByDesc('created_at');
    }
}
