<?php

namespace App\Filament\Clusters\Schools\Resources\SchoolResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class OtherFacilitiesRelationManager extends RelationManager
{
    protected static string $relationship = 'otherFacilities';

    protected static ?string $title = 'Fasilitas Lainnya';

    protected static ?string $modelLabel = 'Fasilitas';

    protected static ?string $pluralModelLabel = 'Daftar Fasilitas';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nama Fasilitas')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (Set $set, ?string $state) {
                        $set('slug', Str::slug($state));
                    }),
                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->disabled()
                    ->dehydrated(),
                Forms\Components\Select::make('type')
                    ->label('Jenis Fasilitas')
                    ->options([
                        'olahraga' => 'Fasilitas Olahraga',
                        'kesenian' => 'Fasilitas Kesenian',
                        'kesehatan' => 'Fasilitas Kesehatan',
                        'keamanan' => 'Fasilitas Keamanan',
                        'parkir' => 'Fasilitas Parkir',
                        'taman' => 'Taman',
                        'lainnya' => 'Lainnya',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('quantity')
                    ->label('Jumlah')
                    ->numeric()
                    ->minValue(1)
                    ->default(1),
                Forms\Components\Select::make('condition')
                    ->label('Kondisi')
                    ->options([
                        'baik' => 'Baik',
                        'rusak_ringan' => 'Rusak Ringan',
                        'rusak_sedang' => 'Rusak Sedang',
                        'rusak_berat' => 'Rusak Berat',
                    ])
                    ->required(),
                Forms\Components\DatePicker::make('purchase_date')
                    ->label('Tanggal Pembelian'),
                Forms\Components\TextInput::make('purchase_price')
                    ->label('Harga Pembelian')
                    ->numeric()
                    ->prefix('Rp'),
                Forms\Components\Textarea::make('description')
                    ->label('Deskripsi')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Fasilitas')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Jenis')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->label('Jumlah')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('condition')
                    ->label('Kondisi')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'baik' => 'success',
                        'rusak_ringan' => 'info',
                        'rusak_sedang' => 'warning',
                        'rusak_berat' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('purchase_date')
                    ->label('Tanggal Pembelian')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('purchase_price')
                    ->label('Harga')
                    ->money('IDR')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Jenis Fasilitas')
                    ->options([
                        'olahraga' => 'Fasilitas Olahraga',
                        'kesenian' => 'Fasilitas Kesenian',
                        'kesehatan' => 'Fasilitas Kesehatan',
                        'keamanan' => 'Fasilitas Keamanan',
                        'parkir' => 'Fasilitas Parkir',
                        'taman' => 'Taman',
                        'lainnya' => 'Lainnya',
                    ]),
                Tables\Filters\SelectFilter::make('condition')
                    ->label('Kondisi Fasilitas')
                    ->options([
                        'baik' => 'Baik',
                        'rusak_ringan' => 'Rusak Ringan',
                        'rusak_sedang' => 'Rusak Sedang',
                        'rusak_berat' => 'Rusak Berat',
                    ]),
                Tables\Filters\Filter::make('needs_repair')
                    ->label('Perlu Perbaikan')
                    ->query(fn(Builder $query): Builder => $query->whereIn('condition', ['rusak_ringan', 'rusak_sedang', 'rusak_berat'])),
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
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }
}
