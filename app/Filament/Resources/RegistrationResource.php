<?php

namespace App\Filament\Resources;

use App\Exports\RegistrationsExport;
use App\Filament\Resources\RegistrationResource\Pages;
use App\Models\AcademyEmailTemplate;
use App\Models\Registration;
use App\Services\AcademyEmailPreviewRenderer;
use App\Services\AcademyRegistrationMailer;
use App\Services\RegistrationPdfExporter;
use App\Support\RegistrationPaymentResumeUrl;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Ressource Filament — inscriptions aux sessions Academy.
 */
class RegistrationResource extends Resource
{
  protected static ?string $model = Registration::class;

  protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

  protected static ?string $navigationGroup = 'SDev Academy';

  protected static ?int $navigationSort = 3;

  protected static ?string $modelLabel = 'Inscription';

  protected static ?string $pluralModelLabel = 'Inscriptions';

  /**
   * Précharge les relations pour le tableau et les exports.
   */
  public static function getEloquentQuery(): Builder
  {
    return parent::getEloquentQuery()->with(['student', 'trainingSession', 'latestPayment']);
  }

  /**
   * Formulaire de création / édition.
   */
  public static function form(Form $form): Form
  {
    return $form
      ->schema([
        Forms\Components\Select::make('training_session_id')
          ->label('Session')
          ->relationship('trainingSession', 'title')
          ->searchable()
          ->preload()
          ->required(),
        Forms\Components\Select::make('student_id')
          ->label('Étudiant')
          ->relationship('student', 'email')
          ->getOptionLabelFromRecordUsing(fn ($record) => $record->full_name.' — '.$record->email)
          ->searchable(['firstname', 'lastname', 'email'])
          ->preload()
          ->required(),
        Forms\Components\Select::make('status')
          ->label('Statut')
          ->options(static::statusOptions())
          ->required(),
        Forms\Components\TextInput::make('source')
          ->label('Source')
          ->default('admin')
          ->maxLength(255),
        Forms\Components\Textarea::make('motivation')
          ->label('Motivation')
          ->rows(4)
          ->columnSpanFull(),
        Forms\Components\Toggle::make('notify_email')
          ->label('Notifications e-mail')
          ->default(true),
        Forms\Components\Toggle::make('notify_sms')
          ->label('Notifications SMS')
          ->default(false),
        Forms\Components\Toggle::make('notify_whatsapp')
          ->label('Notifications WhatsApp')
          ->default(false),
        Forms\Components\DateTimePicker::make('registered_at')
          ->label('Date d\'inscription')
          ->default(now()),
      ]);
  }

  /**
   * Tableau de liste des inscriptions.
   */
  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        Tables\Columns\TextColumn::make('student.full_name')
          ->label('Étudiant')
          ->searchable(['students.firstname', 'students.lastname', 'students.email'])
          ->sortable(['students.lastname']),
        Tables\Columns\TextColumn::make('student.email')
          ->label('E-mail')
          ->searchable()
          ->copyable(),
        Tables\Columns\TextColumn::make('student.phone')
          ->label('Téléphone')
          ->searchable()
          ->copyable()
          ->toggleable(),
        Tables\Columns\TextColumn::make('student.city')
          ->label('Ville')
          ->searchable()
          ->toggleable(),
        Tables\Columns\TextColumn::make('student.country')
          ->label('Pays')
          ->toggleable(),
        Tables\Columns\TextColumn::make('student.education_level')
          ->label('Niveau d\'études')
          ->toggleable(),
        Tables\Columns\TextColumn::make('student.occupation')
          ->label('Profession')
          ->toggleable(),
        Tables\Columns\TextColumn::make('motivation')
          ->label('Motivation')
          ->limit(40)
          ->tooltip(fn (Registration $record): ?string => $record->motivation)
          ->toggleable(),
        Tables\Columns\TextColumn::make('trainingSession.title')
          ->label('Session')
          ->limit(35)
          ->sortable(),
        Tables\Columns\TextColumn::make('status')
          ->label('Statut')
          ->badge()
          ->color(fn (string $state): string => match ($state) {
            'confirmed' => 'success',
            'pending' => 'warning',
            'pending_payment' => 'warning',
            'waitlist' => 'info',
            'pre_registered' => 'info',
            'cancelled' => 'danger',
            default => 'gray',
          })
          ->formatStateUsing(fn (string $state): string => static::statusOptions()[$state] ?? $state),
        Tables\Columns\TextColumn::make('latestPayment.status')
          ->label('Paiement')
          ->badge()
          ->color(fn (?string $state): string => match ($state) {
            'paid' => 'success',
            'pending', 'processing' => 'warning',
            'failed', 'cancelled' => 'danger',
            default => 'gray',
          })
          ->formatStateUsing(fn (?string $state): string => static::paymentStatusOptions()[$state] ?? ($state ?? 'Non initié'))
          ->toggleable(),
        Tables\Columns\TextColumn::make('latestPayment.amount')
          ->label('Montant')
          ->formatStateUsing(function (?string $state, Registration $record): string {
            if ($state === null || $state === '') {
              return '—';
            }

            $currency = $record->latestPayment?->currency ?? '';

            return trim(number_format((float) $state, 2, ',', ' ').' '.$currency);
          })
          ->toggleable(),
        Tables\Columns\TextColumn::make('latestPayment.reference')
          ->label('Réf. paiement')
          ->copyable()
          ->toggleable(isToggledHiddenByDefault: true),
        Tables\Columns\TextColumn::make('source')
          ->label('Source')
          ->toggleable(isToggledHiddenByDefault: true),
        Tables\Columns\IconColumn::make('student.marketing_opt_in')
          ->label('Marketing')
          ->boolean()
          ->toggleable(isToggledHiddenByDefault: true),
        Tables\Columns\IconColumn::make('notify_email')
          ->label('Notif. e-mail')
          ->boolean()
          ->toggleable(isToggledHiddenByDefault: true),
        Tables\Columns\IconColumn::make('notify_sms')
          ->label('Notif. SMS')
          ->boolean()
          ->toggleable(isToggledHiddenByDefault: true),
        Tables\Columns\IconColumn::make('notify_whatsapp')
          ->label('Notif. WhatsApp')
          ->boolean()
          ->toggleable(isToggledHiddenByDefault: true),
        Tables\Columns\TextColumn::make('registered_at')
          ->label('Inscrit le')
          ->dateTime('d/m/Y H:i')
          ->sortable(),
      ])
      ->defaultSort('registered_at', 'desc')
      ->filters([
        Tables\Filters\SelectFilter::make('training_session_id')
          ->label('Session')
          ->relationship('trainingSession', 'title'),
        Tables\Filters\SelectFilter::make('status')
          ->label('Statut')
          ->options(static::statusOptions()),
        Tables\Filters\Filter::make('payment_incomplete')
          ->label('Paiement non finalisé')
          ->query(fn (Builder $query): Builder => $query->paymentIncomplete()),
        Tables\Filters\SelectFilter::make('payment_status')
          ->label('Statut paiement')
          ->options(static::paymentStatusOptions())
          ->query(function (Builder $query, array $data): Builder {
            $value = $data['value'] ?? null;

            if (empty($value)) {
              return $query;
            }

            return $query->whereHas('latestPayment', function (Builder $paymentQuery) use ($value) {
              $paymentQuery->where('status', $value);
            });
          }),
      ])
      ->actions([
        Tables\Actions\Action::make('paymentResumeLink')
          ->label('Lien paiement')
          ->icon('heroicon-o-link')
          ->color('info')
          ->visible(fn (Registration $record): bool => $record->canResumePayment())
          ->modalHeading('Lien de reprise — étape paiement')
          ->modalDescription('Le participant arrive sur le formulaire avec ses informations déjà remplies, directement à l\'étape paiement.')
          ->form([
            Forms\Components\TextInput::make('payment_url')
              ->label('URL à partager')
              ->default(fn (Registration $record): string => RegistrationPaymentResumeUrl::frontendUrl($record))
              ->readOnly()
              ->extraInputAttributes(['onclick' => 'this.select();'])
              ->helperText('Cliquez dans le champ pour tout sélectionner, ou utilisez « Copier le lien ».')
              ->columnSpanFull(),
          ])
          ->extraModalFooterActions([
            Tables\Actions\Action::make('copyPaymentUrl')
              ->label('Copier le lien')
              ->icon('heroicon-o-clipboard-document')
              ->color('success')
              ->action(function (Registration $record, Tables\Actions\Action $action): void {
                $url = RegistrationPaymentResumeUrl::frontendUrl($record);

                $action->getLivewire()->js(
                  'navigator.clipboard.writeText('.json_encode($url).')'
                );

                Notification::make()
                  ->title('Lien copié dans le presse-papiers')
                  ->success()
                  ->send();
              }),
          ])
          ->modalSubmitAction(false)
          ->modalCancelActionLabel('Fermer'),
        Tables\Actions\Action::make('sendEmail')
          ->label('Envoyer un e-mail')
          ->icon('heroicon-o-envelope')
          ->modalHeading('Envoyer un e-mail')
          ->modalSubmitActionLabel('Confirmer et envoyer')
          ->form(function (Registration $record): array {
            $record->loadMissing(['student', 'trainingSession']);

            return static::emailSendFormFields($record);
          })
          ->action(function (Registration $record, array $data): void {
            static::sendTemplatedEmails(collect([$record]), (int) $data['template_id']);
          }),
        Tables\Actions\EditAction::make(),
      ])
      ->bulkActions([
        Tables\Actions\BulkActionGroup::make([
        Tables\Actions\BulkAction::make('sendTemplatedEmail')
            ->label('Envoyer un e-mail')
            ->icon('heroicon-o-envelope')
            ->modalHeading('Envoyer un e-mail')
            ->modalSubmitActionLabel('Confirmer et envoyer')
            ->form(function ($records): array {
              $preview = $records->first();

              if ($preview !== null) {
                $preview->loadMissing(['student', 'trainingSession']);
              }

              return static::emailSendFormFields($preview);
            })
            ->action(function ($records, array $data): void {
              static::sendTemplatedEmails(collect($records), (int) $data['template_id']);
            }),
          Tables\Actions\BulkAction::make('exportExcel')
            ->label('Exporter Excel')
            ->icon('heroicon-o-table-cells')
            ->action(function ($records) {
              $filename = 'inscriptions-selection-'.now()->format('Y-m-d-His').'.xlsx';

              return Excel::download(new RegistrationsExport(collect($records)), $filename);
            }),
          Tables\Actions\BulkAction::make('exportPdf')
            ->label('Exporter PDF')
            ->icon('heroicon-o-document-arrow-down')
            ->action(function ($records) {
              $pdf = app(RegistrationPdfExporter::class)->export(
                collect($records),
                'Inscriptions sélectionnées'
              );
              $filename = 'inscriptions-selection-'.now()->format('Y-m-d-His').'.pdf';

              return response()->streamDownload(
                fn () => print ($pdf->output()),
                $filename,
                ['Content-Type' => 'application/pdf']
              );
            }),
          Tables\Actions\DeleteBulkAction::make(),
        ]),
      ]);
  }

  /**
   * Options de statut d'inscription.
   *
   * @return array<string, string>
   */
  public static function statusOptions(): array
  {
    return [
      'pending' => 'En attente',
      'pending_payment' => 'En attente de paiement',
      'confirmed' => 'Confirmée',
      'waitlist' => 'Liste d\'attente',
      'pre_registered' => 'Pré-inscrit',
      'cancelled' => 'Annulée',
    ];
  }

  /**
   * Options de statut de paiement.
   *
   * @return array<string, string>
   */
  public static function paymentStatusOptions(): array
  {
    return [
      'pending' => 'En attente',
      'processing' => 'En cours',
      'paid' => 'Payé',
      'failed' => 'Échoué',
      'refunded' => 'Remboursé',
      'cancelled' => 'Annulé',
    ];
  }

  /**
   * Schéma du formulaire d'envoi avec aperçu avant confirmation.
   *
   * @param Registration|null $previewRegistration Inscription utilisée pour l'aperçu
   * @return list<Forms\Components\Component>
   */
  public static function emailSendFormFields(?Registration $previewRegistration = null): array
  {
    return [
      Forms\Components\Select::make('template_id')
        ->label('Modèle d\'e-mail')
        ->options(fn (): array => AcademyEmailTemplate::query()->active()->orderBy('name')->pluck('name', 'id')->all())
        ->required()
        ->searchable()
        ->live()
        ->helperText('Utilisez {{lien_paiement}} dans le modèle pour le lien de reprise paiement.'),
      AcademyEmailPreviewRenderer::filamentPreviewField('email_send_preview', function (Get $get) use ($previewRegistration): array {
        return static::resolveEmailPreviewData(
          (int) ($get('template_id') ?? 0),
          $previewRegistration,
          $get('training_session_id') ? (int) $get('training_session_id') : null
        );
      }),
    ];
  }

  /**
   * @deprecated Utiliser emailSendFormFields()
   * @return list<Forms\Components\Component>
   */
  public static function emailTemplateFormSchema(): array
  {
    return static::emailSendFormFields();
  }

  /**
   * Construit les données d'aperçu e-mail pour Filament.
   *
   * @param int $templateId Identifiant du modèle
   * @param Registration|null $previewRegistration Inscription cible
   * @param int|null $sessionFilterId Filtre session (envoi groupé en-tête)
   * @return array{subject: string, body_html: string, payment_resume_url: string|null, firstname: string}
   */
  public static function resolveEmailPreviewData(
    int $templateId,
    ?Registration $previewRegistration = null,
    ?int $sessionFilterId = null
  ): array {
    if ($templateId <= 0) {
      return [
        'subject' => '—',
        'body_html' => '<p class="text-sm text-gray-500">Choisissez un modèle pour voir l\'aperçu.</p>',
        'payment_resume_url' => null,
        'firstname' => '',
      ];
    }

    $template = AcademyEmailTemplate::query()->find($templateId);

    if ($template === null) {
      return [
        'subject' => '—',
        'body_html' => '<p class="text-sm text-red-600">Modèle introuvable.</p>',
        'payment_resume_url' => null,
        'firstname' => '',
      ];
    }

    $registration = $previewRegistration;

    if ($registration === null) {
      $query = Registration::query()->paymentIncomplete()->with(['student', 'trainingSession']);

      if ($sessionFilterId) {
        $query->where('training_session_id', $sessionFilterId);
      }

      $registration = $query->first();
    } else {
      $registration->loadMissing(['student', 'trainingSession']);
    }

    $previewRenderer = app(AcademyEmailPreviewRenderer::class);

    if ($registration !== null) {
      return $previewRenderer->buildRegistrationPreviewData($template, $registration);
    }

    return $previewRenderer->buildSamplePreviewData($template->subject, $template->body);
  }

  /**
   * Envoie un modèle d'e-mail à une collection d'inscriptions.
   *
   * @param \Illuminate\Support\Collection<int, Registration> $records Inscriptions cibles
   * @param int $templateId Identifiant du modèle
   */
  public static function sendTemplatedEmails($records, int $templateId): void
  {
    $template = AcademyEmailTemplate::query()->find($templateId);

    if ($template === null) {
      Notification::make()
        ->title('Modèle introuvable')
        ->danger()
        ->send();

      return;
    }

    $mailer = app(AcademyRegistrationMailer::class);
    $sent = 0;
    $skipped = 0;

    foreach ($records as $record) {
      if ($mailer->send($record, $template)) {
        $sent++;
      } else {
        $skipped++;
      }
    }

    $body = "{$sent} e-mail(s) envoyé(s).";

    if ($skipped > 0) {
      $body .= " {$skipped} ignoré(s) (e-mail manquant ou notifications désactivées).";
    }

    Notification::make()
      ->title('Envoi terminé')
      ->body($body)
      ->success()
      ->send();
  }

  /**
   * @return array<class-string>
   */
  public static function getPages(): array
  {
    return [
      'index' => Pages\ListRegistrations::route('/'),
      'create' => Pages\CreateRegistration::route('/create'),
      'edit' => Pages\EditRegistration::route('/{record}/edit'),
    ];
  }
}
