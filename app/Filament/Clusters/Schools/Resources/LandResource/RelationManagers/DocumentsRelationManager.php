<?php

namespace App\Filament\Clusters\Schools\Resources\LandResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DocumentsRelationManager extends RelationManager
{
    protected static string $relationship = 'documents';

    protected static ?string $title = 'Dokumen Tanah';

    protected static ?string $modelLabel = 'Dokumen';

    protected static ?string $pluralModelLabel = 'Dokumen Tanah';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nama Dokumen')
                    ->required()
                    ->maxLength(255),

                Forms\Components\Select::make('document_type')
                    ->label('Jenis Dokumen')
                    ->required()
                    ->options([
                        'certificate' => 'Sertifikat',
                        'letter' => 'Surat',
                        'report' => 'Laporan',
                        'other' => 'Lainnya',
                    ])
                    ->native(false),

                Forms\Components\FileUpload::make('file_path')
                    ->label('File Dokumen')
                    ->directory('land-documents')
                    ->preserveFilenames()
                    ->downloadable()
                    ->openable()
                    ->required(),

                Forms\Components\DatePicker::make('issue_date')
                    ->label('Tanggal Terbit')
                    ->default(now()),

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
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Dokumen')
                    ->searchable(),

                Tables\Columns\TextColumn::make('document_type')
                    ->label('Jenis')
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'certificate' => 'Sertifikat',
                        'letter' => 'Surat',
                        'report' => 'Laporan',
                        'other' => 'Lainnya',
                        default => $state,
                    })
                    ->badge(),

                Tables\Columns\TextColumn::make('issue_date')
                    ->label('Tanggal Terbit')
                    ->date()
                    ->sortable(),

                Tables\Columns\IconColumn::make('file_path')
                    ->label('File')
                    ->icon(fn(string $state): string => match (pathinfo($state, PATHINFO_EXTENSION)) {
                        'pdf' => 'heroicon-o-document-text',
                        'doc', 'docx' => 'heroicon-o-document',
                        'xls', 'xlsx' => 'heroicon-o-table-cells',
                        default => 'heroicon-o-paper-clip',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('document_type')
                    ->label('Jenis Dokumen')
                    ->options([
                        'certificate' => 'Sertifikat',
                        'letter' => 'Surat',
                        'report' => 'Laporan',
                        'other' => 'Lainnya',
                    ]),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
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
            ]);
    }
}
