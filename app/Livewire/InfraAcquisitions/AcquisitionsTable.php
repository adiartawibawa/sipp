<?php

namespace App\Livewire\InfraAcquisitions;

use App\Models\InfraAcquisition;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Livewire\Component;

class AcquisitionsTable extends Component implements HasForms, HasTable
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
            ->query(fn() => $this->record->acquisitions()->newQuery())
            ->columns([
                Tables\Columns\TextColumn::make('source')
                    ->label('Sumber')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Nilai')
                    ->money('IDR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('year')
                    ->label('Tahun')
                    ->sortable(),

                Tables\Columns\TextColumn::make('method')
                    ->label('Metode'),

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
                        Select::make('source')
                            ->options([
                                'pembelian' => 'Pembelian',
                                'hibah' => 'Hibah',
                                'warisan' => 'Warisan',
                                'lainnya' => 'Lainnya',
                            ])
                            ->required(),
                        TextInput::make('amount')
                            ->numeric()
                            ->prefix('Rp')
                            ->required(),
                        TextInput::make('year')
                            ->numeric()
                            ->minValue(1900)
                            ->maxValue(now()->year)
                            ->required(),
                        Select::make('method')
                            ->options([
                                'tunai' => 'Tunai',
                                'kredit' => 'Kredit',
                                'barter' => 'Barter',
                            ]),
                        Textarea::make('notes')
                            ->label('Catatan')
                            ->columnSpanFull(),
                    ]),

                Tables\Actions\DeleteAction::make()
                    ->icon('heroicon-o-trash')
                    ->color('danger'),
            ])
            ->emptyStateHeading('Belum ada data riwayat perolehan')
            ->emptyStateIcon('heroicon-o-clipboard-document-list');
    }

    public function render()
    {
        return view('livewire.infra-acquisitions.acquisitions-table');
    }
}
