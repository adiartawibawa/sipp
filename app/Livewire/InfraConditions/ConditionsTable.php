<?php

namespace App\Livewire\InfraConditions;

use App\Models\Schools\InfraCondition;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Set;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Livewire\Component;

class ConditionsTable extends Component implements HasForms, HasTable
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
            ->query(fn() => $this->record->conditions()->newQuery())
            ->columns([
                Tables\Columns\TextColumn::make('condition')
                    ->label('Kondisi')
                    ->formatStateUsing(fn($state) => ucwords(str_replace('_', ' ', $state)))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('percentage')
                    ->label('Persentase')
                    ->suffix('%')
                    ->sortable(),

                Tables\Columns\TextColumn::make('checked_at')
                    ->label('Tanggal Pemeriksaan')
                    ->date()
                    ->sortable(),

                Tables\Columns\SpatieMediaLibraryImageColumn::make('document')
                    ->label('Bukti Foto')
                    ->collection('condition_photos')
                    ->conversion('thumb')
                    ->size(40)
                    ->wrap()
                    ->view('livewire.views.tables.columns.photo-column'),

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
                        Select::make('condition')
                            ->options(
                                collect(InfraCondition::defaultInfraCondition())
                                    ->mapWithKeys(fn($data) => [
                                        $data['slug'] => sprintf(
                                            "%s (%d%%)",
                                            ucwords(str_replace('_', ' ', $data['condition'] ?? '')),
                                            $data['percentage'] ?? 0
                                        )
                                    ])
                                    ->toArray()
                            )
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, Set $set) {
                                $selected = collect(InfraCondition::defaultInfraCondition())
                                    ->firstWhere('slug', $state);
                                if ($selected) {
                                    $set('percentage', $selected['percentage']);
                                    $set('notes', $selected['notes']);
                                }
                            }),
                        TextInput::make('percentage')
                            ->numeric()
                            ->suffix('%')
                            ->readOnly(),
                        DatePicker::make('checked_at')
                            ->required(),
                        SpatieMediaLibraryFileUpload::make('photos')
                            ->collection('condition_photos')
                            ->multiple()
                            ->image()
                            ->maxFiles(5)
                            ->label('Bukti Foto')
                            ->downloadable()
                            ->openable()
                            ->preserveFilenames(),
                        Textarea::make('notes')
                            ->label('Catatan')
                            ->columnSpanFull(),
                    ]),

                Tables\Actions\DeleteAction::make()
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->after(function ($record) {
                        $record->clearMediaCollection('condition_photos');
                    }),
            ])
            ->emptyStateHeading('Belum ada data kondisi')
            ->emptyStateIcon('heroicon-o-clipboard-document');
    }

    public function render()
    {
        return view('livewire.infra-conditions.conditions-table');
    }
}
