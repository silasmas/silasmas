<?php

namespace App\Http\Resources\Academy;

use App\Support\CurrencyConverter;
use App\Support\FrontendUrl;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Représentation JSON d'une session de formation.
 */
class TrainingSessionResource extends JsonResource
{
  /**
   * Transforme la session en tableau API.
   *
   * @return array<string, mixed>
   */
  public function toArray(Request $request): array
  {
    return [
      'id' => $this->id,
      'title' => $this->title,
      'slug' => $this->slug,
      'subtitle' => $this->subtitle,
      'description' => $this->description,
      'program' => $this->program,
      'start_date' => $this->start_date?->format('Y-m-d'),
      'end_date' => $this->end_date?->format('Y-m-d'),
      'format' => $this->format,
      'status' => $this->status,
      'max_participants' => $this->max_participants,
      'is_featured' => $this->is_featured,
      'is_free' => (bool) ($this->is_free ?? true),
      'is_paid' => $this->isPaid(),
      'price' => $this->isPaid() ? (float) $this->price : null,
      'currency' => $this->registrationCurrency(),
      'formatted_price' => $this->isPaid()
        ? CurrencyConverter::formatDualLabel(
          (float) $this->price,
          $this->registrationCurrency()
        ) ?? number_format((float) $this->price, 2, ',', ' ').' '.$this->registrationCurrency()
        : null,
      'price_usd' => $this->isPaid()
        ? CurrencyConverter::dualAmounts((float) $this->price, $this->registrationCurrency())['usd']
        : null,
      'price_cdf' => $this->isPaid()
        ? CurrencyConverter::dualAmounts((float) $this->price, $this->registrationCurrency())['cdf']
        : null,
      'exchange_rate_usd_cdf' => CurrencyConverter::usdToCdfRate(),
      'payment_mobile_money_enabled' => $this->acceptsMobileMoneyPayment(),
      'payment_card_enabled' => $this->acceptsCardPayment(),
      'enabled_mobile_operators' => $this->enabledMobileOperators(),
      'cover_image' => $this->coverImageUrl(),
      'cover_image_url' => $this->coverImageUrl(),
      'spot_video_type' => $this->spot_video_type ?? 'none',
      'spot_video_url' => $this->spotVideoUrl(),
      'spot_video_embed_url' => $this->spotVideoEmbedUrl(),
      'spot_video_watch_url' => $this->spotVideoWatchUrl(),
      'spot_video_thumbnail_url' => $this->spotVideoThumbnailUrl(),
      'share_url' => FrontendUrl::to('academy/'.$this->slug),
      'accepts_registrations' => $this->acceptsRegistrations(),
      'active_registrations_count' => $this->when(
        $request->routeIs('academy.sessions.show'),
        fn () => $this->activeRegistrationsCount()
      ),
      'notify_by_email' => (bool) ($this->notify_by_email ?? true),
      'notify_by_sms' => (bool) ($this->notify_by_sms ?? false),
      'notify_by_whatsapp' => (bool) ($this->notify_by_whatsapp ?? false),
      'confidentiality_notice' => $this->when(
        $request->routeIs('academy.sessions.show'),
        $this->confidentiality_notice
      ),
      'participant_benefits' => $this->when(
        $request->routeIs('academy.sessions.show'),
        $this->participant_benefits
      ),
      'registration_benefits' => $this->registrationBenefitsList(),
      'session_resources' => $this->when(
        $request->routeIs('academy.sessions.show'),
        $this->session_resources ?? []
      ),
    ];
  }
}
