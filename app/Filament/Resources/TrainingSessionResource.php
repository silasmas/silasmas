<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TrainingSessionResource\Pages;
use App\Models\TrainingSession;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
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
              ->label('Affiche')
              ->image()
              ->disk('public')
              ->directory('images/academy/sessions')
              ->visibility('public')
              ->maxFiles(1)
              ->imageEditor(),
          ])
          ->columns(2),
        Forms\Components\Section::make('Tarification')
          ->description('Session gratuite ou payante (prix affiché sur le site et exigé à l\'inscription).')
          ->schema([
            Forms\Components\Toggle::make('is_free')
              ->label('Session gratuite')
              ->default(true)
              ->live(),
            Forms\Components\TextInput::make('price')
              ->label('Prix')
              ->numeric()
              ->minValue(0.01)
              ->step(0.01)
              ->visible(fn (Get $get): bool => ! $get('is_free'))
              ->required(fn (Get $get): bool => ! $get('is_free')),
            Forms\Components\Select::make('currency')
              ->label('Devise')
              ->options([
                'USD' => 'USD — Dollar',
                'CDF' => 'CDF — Franc congolais',
                'EUR' => 'EUR — Euro',
              ])
              ->default('USD')
              ->visible(fn (Get $get): bool => ! $get('is_free'))
              ->required(fn (Get $get): bool => ! $get('is_free')),
            Forms\Components\Toggle::make('payment_mobile_money_enabled')
              ->label('Afficher Mobile Money')
              ->default(true)
              ->helperText('Désactivez pour masquer ce moyen sur le formulaire d\'inscription.')
              ->visible(fn (Get $get): bool => ! $get('is_free')),
            Forms\Components\Toggle::make('payment_card_enabled')
              ->label('Afficher carte bancaire')
              ->default(true)
              ->helperText('Désactivez pour masquer ce moyen sur le formulaire d\'inscription.')
              ->visible(fn (Get $get): bool => ! $get('is_free')),
          ])
          ->columns(2),
        Forms\Components\Section::make('Vidéo spot')
          ->schema([
            Forms\Components\Select::make('spot_video_type')
              ->label('Type de vidéo')
              ->options([
                'none' => 'Aucune',
                'file' => 'Fichier (MP4)',
                'youtube' => 'YouTube',
                'vimeo' => 'Vimeo',
              ])
              ->default('none')
              ->live()
              ->required(),
            Forms\Components\FileUpload::make('spot_video')
              ->label('Fichier vidéo')
              ->disk('public')
              ->directory('videos/academy/sessions')
              ->visibility('public')
              ->acceptedFileTypes(['video/mp4', 'video/webm'])
              ->maxFiles(1)
              ->visible(fn (Get $get): bool => $get('spot_video_type') === 'file'),
            Forms\Components\TextInput::make('spot_video_external_url')
              ->label('URL YouTube / Vimeo')
              ->url()
              ->maxLength(500)
              ->visible(fn (Get $get): bool => in_array($get('spot_video_type'), ['youtube', 'vimeo'], true))
              ->columnSpanFull(),
          ])
          ->columns(2),
        Forms\Components\Section::make('Notifications participants')
          ->description('Canaux proposés à l\'inscription ; rappels automatiques la veille du début.')
          ->schema([
            Forms\Components\Toggle::make('notify_by_email')
              ->label('Proposer l\'e-mail')
              ->default(true),
            Forms\Components\Toggle::make('notify_by_sms')
              ->label('Proposer le SMS')
              ->default(false),
            Forms\Components\Toggle::make('notify_by_whatsapp')
              ->label('Proposer WhatsApp')
              ->default(false),
          ])
          ->columns(3),
        Forms\Components\Section::make('Page d\'inscription')
          ->description('Avantages affichés sous le formulaire d\'inscription et dans la modale d\'accueil.')
          ->schema([
            Forms\Components\TagsInput::make('registration_benefits')
              ->label('Avantages inclus')
              ->placeholder('Saisir un avantage puis Entrée')
              ->helperText('Un tag = un avantage. Visible sur le site dès l\'enregistrement.')
              ->splitKeys(['Tab', ','])
              ->columnSpanFull(),
          ]),
        Forms\Components\Section::make('Espace participant & ressources')
          ->schema([
            Forms\Components\Textarea::make('participant_benefits')
              ->label('Droits / avantages affichés')
              ->rows(4)
              ->helperText('Texte visible dans l\'espace participant (accès, support, certificat, etc.).')
              ->columnSpanFull(),
            Forms\Components\Textarea::make('confidentiality_notice')
              ->label('Notice de confidentialité')
              ->rows(6)
              ->helperText('Affichée dans une modale avant d\'ouvrir une ressource.')
              ->columnSpanFull(),
            Forms\Components\Repeater::make('session_resources')
              ->label('Ressources de la session')
              ->schema([
                Forms\Components\TextInput::make('title')
                  ->label('Titre')
                  ->required()
                  ->maxLength(255),
                Forms\Components\TextInput::make('url')
                  ->label('Lien')
                  ->url()
                  ->required()
                  ->maxLength(500),
                Forms\Components\Textarea::make('description')
                  ->label('Description courte')
                  ->rows(2)
                  ->maxLength(500),
              ])
              ->columns(2)
              ->columnSpanFull()
              ->defaultItems(0),
          ]),
      ]);
  }

  /**
   * Tableau de liste des sessions.
   */
  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        Tables\Columns\ImageColumn::make('cover_image')
          ->label('Affiche')
          ->disk('public')
          ->height(56)
          ->square(),
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
        Tables\Columns\TextColumn::make('price')
          ->label('Tarif')
          ->formatStateUsing(function ($state, TrainingSession $record): string {
            if ($record->is_free ?? true) {
              return 'Gratuit';
            }

            return number_format((float) $state, 2).' '.($record->currency ?? 'USD');
          }),
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
