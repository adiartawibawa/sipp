<?php

namespace App\Filament\Clusters\Schools\Resources;

use App\Filament\Clusters\Schools;
use App\Filament\Clusters\Schools\Resources\RoomReferenceResource\Pages;
use App\Filament\Clusters\Schools\Resources\RoomReferenceResource\RelationManagers;
use App\Models\Schools\RoomReference;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

class RoomReferenceResource extends Resource
{
    protected static ?string $model = RoomReference::class;

    protected static ?string $navigationIcon = 'heroicon-o-home-modern';

    protected static ?string $cluster = Schools::class;

    protected static ?string $modelLabel = 'Referensi Ruangan';

    protected static ?string $pluralModelLabel = 'Referensi Ruangan';

    protected static ?string $navigationLabel = 'Ref. Ruangan';

    protected static ?string $recordTitleAttribute = 'full_name';

    protected static ?string $navigationGroup = 'Manajemen Infrastruktur';

    protected static ?int $navigationSort = 21;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Referensi')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Referensi')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $operation, $state, Forms\Set $set) {
                                if ($operation !== 'create') {
                                    return;
                                }

                                $set('slug', Str::slug($state));
                            }),

                        Forms\Components\TextInput::make('code')
                            ->label('Kode Referensi')
                            ->maxLength(50)
                            ->unique(ignoreRecord: true),

                        Forms\Components\TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),

                        Forms\Components\Textarea::make('desc')
                            ->label('Deskripsi')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('full_name')
                    ->label('Nama Lengkap')
                    ->searchable(['name', 'code'])
                    ->sortable(),

                Tables\Columns\TextColumn::make('code')
                    ->label('Kode')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('rooms_count')
                    ->label('Jumlah Ruangan')
                    ->counts('rooms')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Diperbarui Pada')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('has_rooms')
                    ->label('Memiliki Ruangan')
                    ->options([
                        'yes' => 'Ya',
                        'no' => 'Tidak',
                    ])
                    ->query(function (Builder $query, array $data) {
                        if ($data['value'] === 'yes') {
                            $query->has('rooms');
                        } elseif ($data['value'] === 'no') {
                            $query->doesntHave('rooms');
                        }
                    }),

                Tables\Filters\Filter::make('has_code')
                    ->label('Hanya yang memiliki kode')
                    ->query(fn(Builder $query) => $query->whereNotNull('code')),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->before(function (RoomReference $record) {
                        if ($record->rooms()->exists()) {
                            throw new \Exception('Tidak dapat menghapus referensi yang masih memiliki ruangan terkait.');
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->before(function (Collection $records) {
                            $hasRelations = $records->filter(function ($record) {
                                return $record->rooms()->exists();
                            });

                            if ($hasRelations->isNotEmpty()) {
                                throw new \Exception('Beberapa referensi memiliki ruangan terkait dan tidak dapat dihapus.');
                            }
                        }),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\RoomsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRoomReferences::route('/'),
            'create' => Pages\CreateRoomReference::route('/create'),
            'edit' => Pages\EditRoomReference::route('/{record}/edit'),
        ];
    }
}
