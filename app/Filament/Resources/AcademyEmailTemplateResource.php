<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AcademyEmailTemplateResource\Pages;
use App\Models\AcademyEmailTemplate;
use App\Services\AcademyEmailTemplateRenderer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

/**
 * Ressource Filament — modèles d'e-mails Academy personnalisables.
 */
class AcademyEmailTemplateResource extends Resource
{
  protected static ?string $model = AcademyEmailTemplate::class;

  protected static ?string $navigationIcon = 'heroicon-o-envelope-open';

  protected static ?string $navigationGroup = 'SDev Academy';

  protected static ?int $navigationSort = 5;

  protected static ?string $modelLabel = 'Modèle d\'e-mail';

  protected static ?string $pluralModelLabel = 'Modèles d\'e-mails';

  protected static ?string $navigationLabel = 'Modèles e-mails';

  /**
   * Formulaire de création / édition d'un modèle.
   */
  public static function form(Form $form): Form
  {
    $variables = app(AcademyEmailTemplateRenderer::class)->availableVariables();
    $variableHelp = collect($variables)
      ->map(fn (string $label, string $key): string => "{$key} — {$label}")
      ->implode("\n");

    return $form
      ->schema([
        Forms\Components\Section::make('Modèle')
          ->schema([
            Forms\Components\TextInput::make('name')
              ->label('Nom du modèle')
              ->required()
              ->maxLength(255)
              ->helperText('Libellé affiché lors du choix du modèle à l\'envoi.'),
            Forms\Components\TextInput::make('slug')
              ->label('Identifiant')
              ->maxLength(255)
              ->unique(ignoreRecord: true)
              ->helperText('Généré automatiquement si vide.'),
            Forms\Components\Select::make('category')
              ->label('Catégorie')
              ->options([
                'payment_reminder' => 'Relance paiement',
                'general' => 'Général',
                'confirmation' => 'Confirmation',
              ])
              ->default('general')
              ->required(),
            Forms\Components\Toggle::make('is_active')
              ->label('Actif')
              ->default(true),
            Forms\Components\Textarea::make('description')
              ->label('Description interne')
              ->rows(2)
              ->columnSpanFull(),
          ])
          ->columns(2),
        Forms\Components\Section::make('Contenu')
          ->schema([
            Forms\Components\TextInput::make('subject')
              ->label('Objet de l\'e-mail')
              ->required()
              ->maxLength(255)
              ->columnSpanFull(),
            Forms\Components\Textarea::make('body')
              ->label('Corps du message')
              ->required()
              ->rows(12)
              ->columnSpanFull()
              ->helperText('Utilisez les variables ci-dessous telles quelles, sans les entourer de ** ou de guillemets.'),
            Forms\Components\Placeholder::make('variables_help')
              ->label('Variables disponibles')
              ->content(new HtmlString('<pre class="text-xs whitespace-pre-wrap rounded-lg bg-gray-50 p-3 dark:bg-gray-900">'.e($variableHelp).'</pre>'))
              ->columnSpanFull(),
          ]),
      ]);
  }

  /**
   * Tableau de liste des modèles.
   */
  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        Tables\Columns\TextColumn::make('name')
          ->label('Nom')
          ->searchable()
          ->sortable(),
        Tables\Columns\TextColumn::make('category')
          ->label('Catégorie')
          ->badge()
          ->formatStateUsing(fn (string $state): string => match ($state) {
            'payment_reminder' => 'Relance paiement',
            'general' => 'Général',
            'confirmation' => 'Confirmation',
            default => $state,
          }),
        Tables\Columns\TextColumn::make('subject')
          ->label('Objet')
          ->limit(50)
          ->toggleable(),
        Tables\Columns\IconColumn::make('is_active')
          ->label('Actif')
          ->boolean(),
        Tables\Columns\TextColumn::make('updated_at')
          ->label('Modifié le')
          ->dateTime('d/m/Y H:i')
          ->sortable(),
      ])
      ->defaultSort('name')
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
      'index' => Pages\ListAcademyEmailTemplates::route('/'),
      'create' => Pages\CreateAcademyEmailTemplate::route('/create'),
      'edit' => Pages\EditAcademyEmailTemplate::route('/{record}/edit'),
    ];
  }
}
