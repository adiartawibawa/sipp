<?php

namespace App\Livewire\InfraLegalStatus;

use App\Models\InfraLegal;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Livewire\Component;

class LegalStatusTable extends Component implements HasForms, HasTable
{
    use InteractsWithTable;
    use InteractsWithForms;

    public $record;

    public function mount($record)
    {
        $this->record = $record;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn() => $this->record->legalStatuses()->newQuery())
            ->columns([
                Tables\Columns\TextColumn::make('status')
                    ->label('Status Hukum')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('doc_no')
                    ->label('Nomor Dokumen')
                    ->searchable(),

                Tables\Columns\TextColumn::make('doc_date')
                    ->label('Tanggal Dokumen')
                    ->date()
                    ->sortable(),

                Tables\Columns\SpatieMediaLibraryImageColumn::make('document')
                    ->label('Dokumen')
                    ->collection('legal_documents')
                    ->conversion('thumb'),

                Tables\Columns\TextColumn::make('notes')
                    ->label('Catatan')
                    ->limit(50)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        return strlen($state) > 50 ? $state : null;
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->form([
                        Select::make('status')
                            ->options([
                                'sertifikat' => 'Sertifikat',
                                'girik' => 'Girik',
                                'letter_c' => 'Letter C',
                                'lainnya' => 'Lainnya',
                            ])
                            ->required(),
                        TextInput::make('doc_no')
                            ->label('Nomor Dokumen'),
                        DatePicker::make('doc_date')
                            ->label('Tanggal Dokumen'),
                        SpatieMediaLibraryFileUpload::make('document')
                            ->collection('legal_documents')
                            ->label('Upload Dokumen')
                            ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                            ->downloadable()
                            ->openable()
                            ->previewable()
                            ->preserveFilenames()
                            ->maxSize(2048), // 2MB
                        Textarea::make('notes')
                            ->label('Catatan')
                            ->columnSpanFull(),
                    ]),

                Tables\Actions\DeleteAction::make()
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->after(function ($record) {
                        $record->clearMediaCollection('legal_documents');
                    }),
            ])
            ->emptyStateHeading('Belum ada data status hukum')
            ->emptyStateIcon('heroicon-o-scale');
    }

    public function render()
    {
        return view('livewire.infra-legal-status.legal-status-table');
    }
}
