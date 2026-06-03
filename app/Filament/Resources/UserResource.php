<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

/**
 * Ressource Filament pour l'équipe et les comptes admin.
 */
class UserResource extends Resource
{
  protected static ?string $model = User::class;

  protected static ?string $navigationIcon = 'heroicon-o-users';

  protected static ?string $navigationGroup = 'Équipe';

  protected static ?int $navigationSort = 1;

  protected static ?string $modelLabel = 'Utilisateur';

  protected static ?string $pluralModelLabel = 'Utilisateurs';

  /**
   * Formulaire de création / édition.
   */
  public static function form(Form $form): Form
  {
    return $form
      ->schema([
        Forms\Components\Section::make('Identité')
          ->schema([
            Forms\Components\TextInput::make('firstname')
              ->label('Prénom')
              ->maxLength(255),
            Forms\Components\TextInput::make('lastname')
              ->label('Nom')
              ->maxLength(255),
            Forms\Components\TextInput::make('surname')
              ->label('Post-nom')
              ->maxLength(255),
            Forms\Components\Select::make('gender')
              ->label('Genre')
              ->options([
                'M' => 'Masculin',
                'F' => 'Féminin',
              ]),
            Forms\Components\DatePicker::make('birthdate')
              ->label('Date de naissance'),
          ])
          ->columns(2),
        Forms\Components\Section::make('Contact & compte')
          ->schema([
            Forms\Components\TextInput::make('email')
              ->label('E-mail')
              ->email()
              ->maxLength(255)
              ->unique(ignoreRecord: true),
            Forms\Components\TextInput::make('phone')
              ->label('Téléphone')
              ->tel()
              ->maxLength(30)
              ->unique(ignoreRecord: true),
            Forms\Components\TextInput::make('password')
              ->label('Mot de passe')
              ->password()
              ->revealable()
              ->dehydrated(fn ($state) => filled($state))
              ->required(fn (string $operation): bool => $operation === 'create'),
            Forms\Components\Select::make('status_id')
              ->label('Statut')
              ->relationship('status', 'status_name')
              ->searchable()
              ->preload(),
            Forms\Components\Select::make('roles')
              ->label('Rôles')
              ->relationship('roles', 'role_name')
              ->multiple()
              ->preload()
              ->required(),
          ])
          ->columns(2),
        Forms\Components\Section::make('Profil')
          ->schema([
            Forms\Components\FileUpload::make('avatar_url')
              ->label('Photo')
              ->image()
              ->disk('public')
              ->directory('images/avatars')
              ->visibility('public')
              ->imageEditor(),
            Forms\Components\Textarea::make('profile_description')
              ->label('Description')
              ->rows(4)
              ->columnSpanFull(),
          ]),
      ]);
  }

  /**
   * Tableau de liste des utilisateurs.
   */
  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        Tables\Columns\ImageColumn::make('avatar_url')
          ->label('Photo')
          ->disk('public')
          ->circular(),
        Tables\Columns\TextColumn::make('name')
          ->label('Nom')
          ->searchable(['firstname', 'lastname', 'email']),
        Tables\Columns\TextColumn::make('email')
          ->label('E-mail')
          ->searchable()
          ->toggleable(),
        Tables\Columns\TextColumn::make('phone')
          ->label('Téléphone')
          ->toggleable(),
        Tables\Columns\TextColumn::make('roles.role_name')
          ->label('Rôles')
          ->badge(),
        Tables\Columns\TextColumn::make('status.status_name')
          ->label('Statut')
          ->badge(),
      ])
      ->defaultSort('created_at', 'desc')
      ->filters([
        Tables\Filters\SelectFilter::make('roles')
          ->label('Rôle')
          ->relationship('roles', 'role_name')
          ->multiple()
          ->preload(),
      ])
      ->actions([
        Tables\Actions\EditAction::make(),
        Tables\Actions\DeleteAction::make(),
      ])
      ->bulkActions([
        Tables\Actions\BulkActionGroup::make([
          Tables\Actions\DeleteBulkAction::make(),
        ]),
      ]);
  }

  /**
   * @return array<class-string>
   */
  public static function getPages(): array
  {
    return [
      'index' => Pages\ListUsers::route('/'),
      'create' => Pages\CreateUser::route('/create'),
      'edit' => Pages\EditUser::route('/{record}/edit'),
    ];
  }
}
