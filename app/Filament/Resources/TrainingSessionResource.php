<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TrainingSessionResource\Pages;
use App\Models\TrainingSession;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

/**
 * Ressource Filament — sessions de formation SDev Academy.
 */
class TrainingSessionResource extends Resource
{
  protected static ?string $model = TrainingSession::class;

  protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

  protected static ?string $navigationGroup = 'SDev Academy';

  protected static ?int $navigationSort = 1;

  protected static ?string $modelLabel = 'Session';

  protected static ?string $pluralModelLabel = 'Sessions';

  protected static ?string $navigationLabel = 'Sessions';

  /**
   * Formulaire de création / édition.
   */
  public static function form(Form $form): Form
  {
    return $form
      ->schema([
        Forms\Components\Section::make('Informations générales')
          ->schema([
            Forms\Components\TextInput::make('title')
              ->label('Titre')
              ->required()
              ->maxLength(255)
              ->live(onBlur: true)
              ->afterStateUpdated(function (Forms\Set $set, ?string $state) {
                $set('slug', Str::slug($state ?? ''));
              }),
            Forms\Components\TextInput::make('slug')
              ->label('Slug (URL)')
              ->required()
              ->maxLength(255)
              ->unique(ignoreRecord: true),
            Forms\Components\TextInput::make('subtitle')
              ->label('Sous-titre')
              ->maxLength(255)
              ->columnSpanFull(),
            Forms\Components\Textarea::make('description')
              ->label('Description')
              ->rows(4)
              ->columnSpanFull(),
            Forms\Components\Textarea::make('program')
              ->label('Programme')
              ->rows(8)
              ->columnSpanFull(),
          ])
          ->columns(2),
        Forms\Components\Section::make('Planification')
          ->schema([
            Forms\Components\DatePicker::make('start_date')
              ->label('Date de début')
              ->required(),
            Forms\Components\DatePicker::make('end_date')
              ->label('Date de fin')
              ->required(),
            Forms\Components\Select::make('format')
              ->label('Format')
              ->options([
                'online' => 'En ligne',
                'onsite' => 'Présentiel',
                'hybrid' => 'Hybride',
              ])
              ->required(),
            Forms\Components\Select::make('status')
              ->label('Statut')
              ->options([
                'draft' => 'Brouillon',
                'open' => 'Inscriptions ouvertes',
                'closed' => 'Inscriptions fermées',
                'completed' => 'Terminée',
              ])
              ->required(),
            Forms\Components\TextInput::make('max_participants')
              ->label('Places max')
              ->numeric()
              ->minValue(1),
            Forms\Components\Toggle::make('is_featured')
              ->label('Mettre en avant'),
            Forms\Components\FileUpload::make('cover_image')
              ->label('Visuel')
              ->image()
              ->disk('public')
              ->directory('images/academy/sessions')
              ->visibility('public'),
          ])
          ->columns(2),
      ]);
  }

  /**
   * Tableau de liste des sessions.
   */
  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        Tables\Columns\TextColumn::make('title')
          ->label('Titre')
          ->searchable()
          ->sortable()
          ->limit(40),
        Tables\Columns\TextColumn::make('start_date')
          ->label('Début')
          ->date('d/m/Y')
          ->sortable(),
        Tables\Columns\TextColumn::make('format')
          ->label('Format')
          ->badge()
          ->formatStateUsing(fn (string $state): string => match ($state) {
            'online' => 'En ligne',
            'onsite' => 'Présentiel',
            'hybrid' => 'Hybride',
            default => $state,
          }),
        Tables\Columns\TextColumn::make('status')
          ->label('Statut')
          ->badge()
          ->color(fn (string $state): string => match ($state) {
            'open' => 'success',
            'draft' => 'gray',
            'closed' => 'warning',
            'completed' => 'info',
            default => 'gray',
          }),
        Tables\Columns\TextColumn::make('registrations_count')
          ->label('Inscrits')
          ->counts('registrations'),
        Tables\Columns\IconColumn::make('is_featured')
          ->label('Vedette')
          ->boolean(),
      ])
      ->defaultSort('start_date', 'desc')
      ->filters([
        Tables\Filters\SelectFilter::make('status')
          ->label('Statut')
          ->options([
            'draft' => 'Brouillon',
            'open' => 'Ouvert',
            'closed' => 'Fermé',
            'completed' => 'Terminé',
          ]),
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
      'index' => Pages\ListTrainingSessions::route('/'),
      'create' => Pages\CreateTrainingSession::route('/create'),
      'edit' => Pages\EditTrainingSession::route('/{record}/edit'),
    ];
  }
}
