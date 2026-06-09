<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SiteBlockResource\Pages;
use App\Models\SiteBlock;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

/**
 * Ressource Filament — contenu dynamique du site vitrine.
 */
class SiteBlockResource extends Resource
{
  protected static ?string $model = SiteBlock::class;

  protected static ?string $navigationIcon = 'heroicon-o-document-text';

  protected static ?string $navigationGroup = 'Site vitrine';

  protected static ?int $navigationSort = 0;

  protected static ?string $modelLabel = 'Bloc de contenu';

  protected static ?string $pluralModelLabel = 'Contenu du site';

  protected static ?string $navigationLabel = 'Contenu du site';

  /**
   * Libellés des groupes de blocs.
   *
   * @return array<string, string>
   */
  public static function groupOptions(): array
  {
    return [
      'hero' => 'Hero (accueil)',
      'about' => 'À propos',
      'skill' => 'Compétence',
      'service' => 'Service studio',
      'testimonial' => 'Témoignage',
      'principle' => 'Principe (manifeste)',
      'faq' => 'FAQ',
      'client_logo' => 'Logo client (bandeau)',
      'hero_tagline' => 'Expertise hero (rotation)',
      'silas' => 'Silas — Hero',
      'silas_availability' => 'Silas — Disponibilité',
      'silas_journey_intro' => 'Silas — Intro trajectoire',
      'silas_journey' => 'Silas — Étape trajectoire',
      'silas_banner' => 'Silas — Bannière conférence',
      'silas_offer' => 'Silas — Offre conseil',
      'silas_cta' => 'Silas — Appel à action',
    ];
  }

  /**
   * Groupes de la page Silas (singletons).
   *
   * @return array<int, string>
   */
  protected static function silasSingletonGroups(): array
  {
    return [
      'silas',
      'silas_availability',
      'silas_journey_intro',
      'silas_banner',
      'silas_cta',
    ];
  }

  /**
   * Formulaire de création / édition.
   */
  public static function form(Form $form): Form
  {
    return $form
      ->schema([
        Forms\Components\Select::make('group')
          ->label('Section')
          ->options(static::groupOptions())
          ->required()
          ->live(),
        Forms\Components\TextInput::make('title')
          ->label(fn (Get $get): string => match ($get('group')) {
            'testimonial' => 'Auteur',
            'faq' => 'Question',
            'client_logo' => 'Nom du client',
            'hero', 'silas' => 'Titre principal',
            'silas_availability' => 'Période (ex. Q3 — 2026)',
            'silas_journey_intro' => 'Titre de section',
            'silas_banner' => 'Titre sur l\'image',
            'silas_cta' => 'Titre CTA',
            'silas_journey' => 'Titre de l\'étape',
            'silas_offer' => 'Titre de l\'offre',
            default => 'Titre',
          })
          ->required()
          ->maxLength(255)
          ->columnSpanFull(),
        Forms\Components\TextInput::make('subtitle')
          ->label(fn (Get $get): string => match ($get('group')) {
            'testimonial' => 'Rôle / fonction',
            'service' => 'Extrait court',
            'hero', 'silas' => 'Eyebrow',
            'silas_journey' => 'Année',
            'silas_banner' => 'Badge (ex. Conférence — Dakar, 2025)',
            'silas_cta' => 'Texte du bouton',
            default => 'Sous-titre / eyebrow',
          })
          ->maxLength(255)
          ->visible(fn (Get $get): bool => in_array($get('group'), [
            'about', 'hero', 'testimonial', 'service', 'silas', 'silas_journey',
            'silas_banner', 'silas_cta',
          ], true))
          ->columnSpanFull(),
        Forms\Components\Textarea::make('body')
          ->label(fn (Get $get): string => match ($get('group')) {
            'testimonial' => 'Citation',
            'faq' => 'Réponse',
            'hero', 'silas' => 'Description / introduction',
            'silas_availability' => 'Note de disponibilité',
            'silas_journey_intro' => 'Introduction de section',
            'silas_journey', 'silas_offer' => 'Description',
            'silas_cta' => 'Sous-titre CTA',
            default => 'Description',
          })
          ->rows(4)
          ->visible(fn (Get $get): bool => in_array($get('group'), [
            'about', 'service', 'hero', 'testimonial', 'principle', 'faq',
            'silas', 'silas_availability', 'silas_journey_intro', 'silas_journey',
            'silas_offer', 'silas_cta',
          ], true))
          ->columnSpanFull(),
        Forms\Components\Textarea::make('secondary_body')
          ->label(fn (Get $get): string => match ($get('group')) {
            'hero', 'silas' => 'Accent italique (partie colorée du titre)',
            default => 'Texte secondaire',
          })
          ->rows(3)
          ->visible(fn (Get $get): bool => in_array($get('group'), ['about', 'hero', 'silas'], true))
          ->columnSpanFull(),
        Forms\Components\Select::make('icon')
          ->label('Icône')
          ->options(fn (Get $get): array => match ($get('group')) {
            'silas_offer' => [
              'compass' => 'Boussole (conseil)',
              'lightbulb' => 'Ampoule (audit)',
              'mic' => 'Micro (conférence)',
            ],
            default => [
              'globe' => 'Globe (web)',
              'mobile' => 'Mobile',
              'marketing' => 'Marketing',
              'design' => 'Design',
              'ia' => 'IA',
            ],
          })
          ->visible(fn (Get $get): bool => in_array($get('group'), ['service', 'silas_offer'], true)),
        Forms\Components\TextInput::make('level')
          ->label('Niveau (%)')
          ->numeric()
          ->minValue(0)
          ->maxValue(100)
          ->visible(fn (Get $get): bool => $get('group') === 'skill'),
        Forms\Components\FileUpload::make('image')
          ->label('Image')
          ->image()
          ->disk('public')
          ->directory('images/site')
          ->visibility('public')
          ->maxFiles(1)
          ->visible(fn (Get $get): bool => in_array($get('group'), [
            'about', 'hero', 'silas', 'silas_banner',
          ], true)),
        Forms\Components\TextInput::make('sort_order')
          ->label('Ordre')
          ->numeric()
          ->default(0)
          ->minValue(0),
        Forms\Components\Toggle::make('is_published')
          ->label('Publié')
          ->default(true),
      ])
      ->columns(2);
  }

  /**
   * Tableau de liste des blocs.
   */
  public static function table(Table $table): Table
  {
    $labels = static::groupOptions();

    return $table
      ->columns([
        Tables\Columns\TextColumn::make('group')
          ->label('Section')
          ->badge()
          ->formatStateUsing(fn (string $state): string => $labels[$state] ?? $state),
        Tables\Columns\ImageColumn::make('image')
          ->label('Image')
          ->disk('public')
          ->height(40)
          ->toggleable(),
        Tables\Columns\TextColumn::make('title')
          ->label('Titre')
          ->searchable()
          ->limit(50),
        Tables\Columns\TextColumn::make('level')
          ->label('%')
          ->suffix('%')
          ->toggleable(),
        Tables\Columns\IconColumn::make('is_published')
          ->label('Publié')
          ->boolean(),
        Tables\Columns\TextColumn::make('sort_order')
          ->label('Ordre')
          ->sortable(),
      ])
      ->defaultSort('sort_order')
      ->filters([
        Tables\Filters\SelectFilter::make('group')
          ->label('Section')
          ->options(static::groupOptions()),
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
      'index' => Pages\ListSiteBlocks::route('/'),
      'create' => Pages\CreateSiteBlock::route('/create'),
      'edit' => Pages\EditSiteBlock::route('/{record}/edit'),
    ];
  }
}
