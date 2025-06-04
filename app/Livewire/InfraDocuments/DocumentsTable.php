<?php

namespace App\Livewire\InfraDocuments;

use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Livewire\Component;

class DocumentsTable extends Component implements HasForms, HasTable
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
            ->query(fn() => $this->record->documents()->newQuery())
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Dokumen'),

                Tables\Columns\TextColumn::make('path')
                    ->label('Nama File')
                    ->formatStateUsing(fn($state) => basename($state)),

                Tables\Columns\TextColumn::make('path')
                    ->label('Unduh')
                    ->url(fn($record) => asset('storage/' . $record->path), true)
                    ->openUrlInNewTab()
                    ->icon('heroicon-o-arrow-down-tray'),
            ])
            ->actions([
                Tables\Actions\DeleteAction::make()
                    ->icon('heroicon-o-trash')
                    ->color('danger'),
            ]);
    }

    public function render()
    {
        return view('livewire.infra-documents.documents-table');
    }
}
