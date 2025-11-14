<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'Users';
    protected static ?string $pluralLabel = 'Users';
    protected static ?string $navigationGroup = 'Manajemen Pengguna';
    protected static ?string $slug = 'users';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Data Pengguna')
                ->schema([
                    Forms\Components\TextInput::make('nip')
                        ->label('NIP')
                        ->nullable()
                        ->maxLength(30)
                        ->placeholder('Masukkan NIP (opsional)'),

                    Forms\Components\TextInput::make('name')
                        ->label('Nama Lengkap')
                        ->required()
                        ->maxLength(255),

                    Forms\Components\TextInput::make('email')
                        ->label('Email')
                        ->email()
                        ->unique(ignoreRecord: true)
                        ->required(),

                    Forms\Components\TextInput::make('password')
                        ->label('Password')
                        ->password()
                        ->revealable()
                        ->dehydrateStateUsing(fn($state) => filled($state) ? Hash::make($state) : null)
                        ->dehydrated(fn($state) => filled($state))
                        ->required(fn(string $operation) => $operation === 'create')
                        ->placeholder('Isi jika ingin mengganti password'),

                    Forms\Components\Select::make('roles')
                        ->label('Role')
                        ->relationship('roles', 'name')
                        ->preload()
                        ->multiple(false)
                        ->required(),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('nip')->label('NIP')->sortable()->searchable(),
            TextColumn::make('name')->label('Nama')->sortable()->searchable(),
            TextColumn::make('email')->label('Email')->sortable()->searchable(),
            Tables\Columns\BadgeColumn::make('roles.name')
                ->label('Role')
                ->colors([
                    'success' => 'admin',
                    'info' => 'user',
                    'warning' => 'operator',
                ]),
            TextColumn::make('created_at')
                ->label('Dibuat')
                ->dateTime('d M Y H:i')
                ->sortable(),
        ])
            ->filters([])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
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
}
