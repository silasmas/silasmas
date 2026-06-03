<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MessageResource\Pages;
use App\Models\Message;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

/**
 * Ressource Filament pour les messages internes.
 */
class MessageResource extends Resource
{
  protected static ?string $model = Message::class;

  protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

  protected static ?string $navigationGroup = 'Contenu';

  protected static ?int $navigationSort = 2;

  protected static ?string $modelLabel = 'Message';

  protected static ?string $pluralModelLabel = 'Messages';

  /**
   * Formulaire de création / édition.
   */
  public static function form(Form $form): Form
  {
    return $form
      ->schema([
        Forms\Components\TextInput::make('message_subject')
          ->label('Sujet')
          ->required()
          ->maxLength(255)
          ->columnSpanFull(),
        Forms\Components\Textarea::make('message_content')
          ->label('Contenu')
          ->required()
          ->rows(6)
          ->columnSpanFull(),
        Forms\Components\Select::make('status_id')
          ->label('Statut')
          ->relationship('status', 'status_name')
          ->searchable()
          ->preload(),
        Forms\Components\Select::make('user_id')
          ->label('Utilisateur lié')
          ->relationship('user', 'email')
          ->getOptionLabelFromRecordUsing(fn ($record) => $record->name)
          ->searchable(['firstname', 'lastname', 'email'])
          ->preload(),
      ]);
  }

  /**
   * Tableau de liste des messages.
   */
  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        Tables\Columns\TextColumn::make('message_subject')
          ->label('Sujet')
          ->searchable()
          ->limit(40),
        Tables\Columns\TextColumn::make('status.status_name')
          ->label('Statut')
          ->badge(),
        Tables\Columns\TextColumn::make('user.name')
          ->label('Utilisateur')
          ->toggleable(),
        Tables\Columns\TextColumn::make('created_at')
          ->label('Reçu le')
          ->dateTime('d/m/Y H:i')
          ->sortable(),
      ])
      ->defaultSort('created_at', 'desc')
      ->actions([
        Tables\Actions\ViewAction::make(),
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
      'index' => Pages\ListMessages::route('/'),
      'create' => Pages\CreateMessage::route('/create'),
      'edit' => Pages\EditMessage::route('/{record}/edit'),
    ];
  }
}
