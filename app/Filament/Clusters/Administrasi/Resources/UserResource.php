<?php

namespace App\Filament\Clusters\Administrasi\Resources;

use App\Filament\Clusters\Administrasi;
use App\Filament\Clusters\Administrasi\Resources\UserResource\Pages;
use App\Filament\Clusters\Administrasi\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $cluster = Administrasi::class;

    protected static ?string $navigationLabel = 'Users';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('User Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('username')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        SpatieMediaLibraryFileUpload::make('avatar')
                            ->collection('avatar')
                            ->image()
                            ->imageEditor(),
                    ])->columns(2),

                Forms\Components\Section::make('Security')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                User::STATUS_ACTIVE => 'Active',
                                User::STATUS_INACTIVE => 'Inactive',
                                User::STATUS_PENDING => 'Pending',
                            ])
                            ->required(),
                        Forms\Components\Toggle::make('email_verified_at')
                            ->label('Email Verified')
                            ->formatStateUsing(fn($state) => !is_null($state))
                            ->dehydrateStateUsing(fn($state) => $state ? now() : null),
                        Forms\Components\TextInput::make('password')
                            ->password()
                            ->rule(Password::default())
                            ->dehydrateStateUsing(fn($state) => Hash::make($state))
                            ->dehydrated(fn($state) => filled($state))
                            ->required(fn(string $context): bool => $context === 'create'),
                    ]),

                Forms\Components\Section::make('Roles & Permissions')
                    ->schema([
                        Forms\Components\CheckboxList::make('roles')
                            ->relationship('roles', 'name')
                            ->searchable(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\SpatieMediaLibraryImageColumn::make('avatar')
                    ->collection('avatar')
                    ->label('Avatar')
                    ->circular(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('username')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        User::STATUS_ACTIVE => 'success',
                        User::STATUS_INACTIVE => 'danger',
                        User::STATUS_PENDING => 'warning',
                    }),
                Tables\Columns\IconColumn::make('email_verified_at')
                    ->label('Verified')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        User::STATUS_ACTIVE => 'Active',
                        User::STATUS_INACTIVE => 'Inactive',
                        User::STATUS_PENDING => 'Pending',
                    ]),
                Tables\Filters\Filter::make('verified')
                    ->query(fn($query) => $query->whereNotNull('email_verified_at'))
                    ->label('Verified Users'),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('verify')
                    ->icon('heroicon-o-check-circle')
                    ->action(fn(User $user) => $user->markAsVerified())
                    ->hidden(fn(User $user) => $user->hasVerifiedEmail()),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                    Tables\Actions\BulkAction::make('markAsActive')
                        ->label('Mark as Active')
                        ->action(fn($records) => $records->each->update(['status' => User::STATUS_ACTIVE])),
                    Tables\Actions\BulkAction::make('markAsInactive')
                        ->label('Mark as Inactive')
                        ->action(fn($records) => $records->each->update(['status' => User::STATUS_INACTIVE])),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
