<?php

namespace App\Filament\Clusters\Schools\Resources\RoomResource\RelationManagers;

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

    protected static ?string $title = 'Dokumen';

    protected static ?string $modelLabel = 'Dokumen';

    protected static ?string $pluralModelLabel = 'Daftar Dokumen';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(100)
                    ->label('Nama Dokumen'),
                Forms\Components\FileUpload::make('path')
                    ->required()
                    ->label('File Dokumen')
                    ->directory('room-documents')
                    ->preserveFilenames()
                    ->acceptedFileTypes([
                        'application/pdf',
                        'application/msword',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        'image/*',
                    ])
                    ->maxSize(5120)
                    ->downloadable(),
                Forms\Components\Textarea::make('notes')
                    ->label('Catatan')
                    ->maxLength(500)
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
                Tables\Columns\TextColumn::make('path')
                    ->label('Tipe File')
                    ->formatStateUsing(fn(InfraDocument $record): string => Str::upper($record->extension))
                    ->badge(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Diunggah')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\IconColumn::make('path')
                    ->label('Unduh')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn(InfraDocument $record): string => $record->url)
                    ->openUrlInNewTab(),
            ])
            ->filters([
                Tables\Filters\Filter::make('has_file')
                    ->label('Hanya yang memiliki file')
                    ->query(fn(Builder $query): Builder => $query->whereNotNull('path')),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Unggah Dokumen'),
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
            ->defaultSort('created_at', 'desc');
    }
}
