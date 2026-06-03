<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudentResource\Pages;
use App\Models\Student;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

/**
 * Ressource Filament — base étudiants SDev Academy.
 */
class StudentResource extends Resource
{
  protected static ?string $model = Student::class;

  protected static ?string $navigationIcon = 'heroicon-o-user-group';

  protected static ?string $navigationGroup = 'SDev Academy';

  protected static ?int $navigationSort = 2;

  protected static ?string $modelLabel = 'Étudiant';

  protected static ?string $pluralModelLabel = 'Étudiants';

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
              ->required()
              ->maxLength(255),
            Forms\Components\TextInput::make('lastname')
              ->label('Nom')
              ->required()
              ->maxLength(255),
            Forms\Components\TextInput::make('email')
              ->label('E-mail')
              ->email()
              ->required()
              ->maxLength(255)
              ->unique(ignoreRecord: true),
            Forms\Components\TextInput::make('phone')
              ->label('Téléphone')
              ->tel()
              ->maxLength(30),
          ])
          ->columns(2),
        Forms\Components\Section::make('Profil')
          ->schema([
            Forms\Components\TextInput::make('city')
              ->label('Ville')
              ->maxLength(255),
            Forms\Components\TextInput::make('country')
              ->label('Pays')
              ->default('RDC')
              ->maxLength(100),
            Forms\Components\TextInput::make('education_level')
              ->label('Niveau d\'études')
              ->maxLength(255),
            Forms\Components\TextInput::make('occupation')
              ->label('Profession / activité')
              ->maxLength(255),
            Forms\Components\Toggle::make('marketing_opt_in')
              ->label('Accepte les communications SDev Academy')
              ->default(true),
            Forms\Components\Textarea::make('notes')
              ->label('Notes internes')
              ->rows(3)
              ->columnSpanFull(),
          ])
          ->columns(2),
      ]);
  }

  /**
   * Tableau de liste des étudiants.
   */
  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        Tables\Columns\TextColumn::make('full_name')
          ->label('Nom')
          ->searchable(['firstname', 'lastname', 'email'])
          ->sortable(['firstname']),
        Tables\Columns\TextColumn::make('email')
          ->label('E-mail')
          ->searchable()
          ->copyable(),
        Tables\Columns\TextColumn::make('phone')
          ->label('Téléphone')
          ->toggleable(),
        Tables\Columns\TextColumn::make('city')
          ->label('Ville')
          ->toggleable(),
        Tables\Columns\TextColumn::make('registrations_count')
          ->label('Inscriptions')
          ->counts('registrations'),
        Tables\Columns\IconColumn::make('marketing_opt_in')
          ->label('Com.')
          ->boolean()
          ->toggleable(),
        Tables\Columns\TextColumn::make('created_at')
          ->label('Créé le')
          ->dateTime('d/m/Y')
          ->sortable()
          ->toggleable(isToggledHiddenByDefault: true),
      ])
      ->defaultSort('created_at', 'desc')
      ->filters([
        Tables\Filters\TernaryFilter::make('marketing_opt_in')
          ->label('Communications acceptées'),
      ])
      ->actions([
        Tables\Actions\EditAction::make(),
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
      'index' => Pages\ListStudents::route('/'),
      'create' => Pages\CreateStudent::route('/create'),
      'edit' => Pages\EditStudent::route('/{record}/edit'),
    ];
  }
}
