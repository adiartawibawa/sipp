<?php

namespace App\Filament\Clusters\Schools\Resources\BuildingResource\RelationManagers;

use App\Models\Schools\InfraDocument;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class DocumentsRelationManager extends RelationManager
{
    protected static string $relationship = 'documents';

    protected static ?string $modelLabel = 'Dokumen';

    protected static ?string $pluralModelLabel = 'Dokumen Bangunan';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nama Dokumen')
                    ->required()
                    ->maxLength(100)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (string $operation, $state, Forms\Set $set) {
                        if ($operation !== 'create') {
                            return;
                        }

                        $set('slug', Str::slug($state));
                    }),

                Forms\Components\TextInput::make('slug')
                    ->label('Slug')
                    ->required()
                    ->maxLength(100)
                    ->unique(ignoreRecord: true),

                Forms\Components\Select::make('document_type_id')
                    ->label('Jenis Dokumen')
                    ->relationship('type', 'name')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->native(false),

                Forms\Components\DatePicker::make('issued_at')
                    ->label('Tanggal Dikeluarkan')
                    ->required(),

                Forms\Components\DatePicker::make('expired_at')
                    ->label('Tanggal Kadaluarsa')
                    ->after('issued_at'),

                Forms\Components\FileUpload::make('file_path')
                    ->label('File Dokumen')
                    ->directory('building-documents')
                    ->preserveFilenames()
                    ->downloadable()
                    ->openable()
                    ->previewable(false)
                    ->acceptedFileTypes([
                        'application/pdf',
                        'image/jpeg',
                        'image/png',
                        'application/msword',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    ])
                    ->maxSize(10240)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Dokumen')
                    ->searchable(),

                Tables\Columns\TextColumn::make('type.name')
                    ->label('Jenis')
                    ->searchable(),

                Tables\Columns\TextColumn::make('issued_at')
                    ->label('Tanggal Dikeluarkan')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('expired_at')
                    ->label('Kadaluarsa')
                    ->date()
                    ->sortable()
                    ->color(fn($record) => $record->expired_at && $record->expired_at->isPast() ? 'danger' : null),

                Tables\Columns\IconColumn::make('file_path')
                    ->label('File')
                    ->icon(fn($record) => $record->file_path ? 'heroicon-o-document-text' : 'heroicon-o-x-circle')
                    ->color(fn($record) => $record->file_path ? 'success' : 'danger'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->relationship('type', 'name')
                    ->label('Filter Jenis Dokumen')
                    ->searchable()
                    ->preload(),

                Tables\Filters\Filter::make('expired')
                    ->label('Dokumen Kadaluarsa')
                    ->query(fn(Builder $query): Builder => $query->whereNotNull('expired_at')->where('expired_at', '<', now())),
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
            ->defaultSort('issued_at', 'desc');
    }

    protected function getTableQuery(): Builder
    {
        return parent::getTableQuery()
            ->with(['type'])
            ->latest('issued_at');
    }
}
