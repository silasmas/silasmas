"use client";

import Link from "next/link";
import { usePathname } from "next/navigation";
import { useEffect, useState } from "react";
import { RegistrationOpensCountdown } from "@/components/academy/RegistrationOpensCountdown";
import { SessionPoster } from "@/components/academy/SessionPoster";
import { SessionRegistrationBenefits } from "@/components/academy/SessionRegistrationBenefits";
import { useSpotVideoModal } from "@/components/academy/SpotVideoModal";
import { useRegistrationBenefits } from "@/hooks/useRegistrationBenefits";
import { RichHtmlContent } from "@/components/site/RichHtmlContent";
import { richTextExcerpt } from "@/lib/rich-html";
import { pickPrimarySession } from "@/lib/sessions";
import type { ApiResponse, TrainingSession } from "@/types/api";

const API_BASE =
  process.env.NEXT_PUBLIC_API_URL ?? "http://localhost:8000/api";

interface ActiveSessionPromoProps {
  session: TrainingSession | null;
}

const DISMISS_PREFIX = "sdev-academy-promo-dismissed-";

/**
 * Indique si la promo doit être masquée sur cette route.
 *
 * @param pathname Chemin courant
 * @return true sur l'espace participant uniquement
 */
function isPromoHiddenPath(pathname: string): boolean {
  return pathname.startsWith("/academy/espace/");
}

/**
 * Page d'inscription /academy/{slug} (FAB décalé pour ne pas gêner le formulaire).
 *
 * @param pathname Chemin courant
 * @return true sur la page session
 */
function isAcademyRegistrationPath(pathname: string): boolean {
  return /^\/academy\/[^/]+$/.test(pathname);
}

/**
 * Indique si la session peut afficher la modale promo (ouverte ou pré-inscription).
 *
 * @param session Session Academy
 * @return true si la promo est pertinente
 */
function sessionShowsPromo(session: TrainingSession): boolean {
  return session.status === "open" || Boolean(session.shows_pre_registration_page);
}

/**
 * Libellé formaté de la date d'ouverture des inscriptions.
 *
 * @param session Session Academy
 * @return Texte affichable ou chaîne vide
 */
function registrationOpensLabel(session: TrainingSession): string {
  if (session.registration_opens_at_label) {
    return session.registration_opens_at_label;
  }

  if (!session.registration_opens_at) {
    return "";
  }

  return new Date(session.registration_opens_at).toLocaleString("fr-FR");
}

function ActiveSessionPromoContent({ session }: { session: TrainingSession }) {
  const pathname = usePathname();
  const isRegistrationPage = isAcademyRegistrationPath(pathname);
  const isPreRegistration = Boolean(session.shows_pre_registration_page);
  const [modalOpen, setModalOpen] = useState(false);
  const [fabVisible, setFabVisible] = useState(false);
  const { openModal, SpotVideoModal } = useSpotVideoModal(session);

  const hasVideo =
    session.spot_video_type !== "none" &&
    Boolean(session.spot_video_embed_url || session.spot_video_url);

  useEffect(() => {
    const dismissKey = `${DISMISS_PREFIX}${session.slug}`;
    const wasDismissed = window.localStorage.getItem(dismissKey) === "1";

    if (wasDismissed) {
      setFabVisible(true);
      return;
    }

    setModalOpen(true);
  }, [session.slug]);

  const dismissKey = `${DISMISS_PREFIX}${session.slug}`;
  const priceLabel =
    session.formatted_price
    ?? (session.is_paid && session.price != null
      ? `${session.price} ${session.currency ?? "USD"}`
      : "Gratuit");
  const benefits = useRegistrationBenefits(session.slug, session);
  const opensLabel = registrationOpensLabel(session);
  const promoExcerpt =
    richTextExcerpt(
      isPreRegistration ? session.pre_registration_message : session.description,
      220,
    )
    || (isPreRegistration ? session.pre_registration_message : session.description);
  const showPromoMessage = isPreRegistration
    ? Boolean(session.pre_registration_message?.trim())
    : Boolean(promoExcerpt?.trim());
  const eyebrow = isPreRegistration
    ? "Pré-inscription — SDev Academy"
    : "Session en cours — SDev Academy";
  const ctaLabel = isPreRegistration ? "Me pré-inscrire" : "S'inscrire maintenant";
  const ctaHref = isPreRegistration
    ? `/academy/${session.slug}`
    : `/academy/${session.slug}#inscription`;
  const fabLabel = isPreRegistration ? "Pré-inscription" : "Session Academy";
  const fabAriaLabel = isPreRegistration
    ? "Voir la pré-inscription Academy"
    : "Voir la session Academy en cours";

  const closeModal = () => {
    setModalOpen(false);
    window.localStorage.setItem(dismissKey, "1");
    setFabVisible(true);
  };

  return (
    <>
      {modalOpen && (
        <div
          className="session-modal-overlay"
          role="dialog"
          aria-modal="true"
          aria-labelledby="session-promo-title"
        >
          <div className="session-modal session-modal-promo">
            <button
              type="button"
              className="session-modal-close"
              onClick={closeModal}
              aria-label="Fermer"
            >
              ×
            </button>

            <div className="session-modal-grid session-modal-grid-promo">
              <div className="session-modal-poster-col">
                <SessionPoster
                  session={session}
                  priority
                  variant="modal"
                  preferPreRegistrationCover={isPreRegistration}
                />
              </div>
              <div className="session-modal-content">
                <p className="section-eyebrow mb-2">{eyebrow}</p>
                <h2
                  id="session-promo-title"
                  className="font-display mb-2 text-2xl leading-tight tracking-tight md:text-3xl"
                >
                  {session.title}
                </h2>
                {session.subtitle && (
                  <p className="mb-3 text-sm text-muted md:text-base line-clamp-2">
                    {session.subtitle}
                  </p>
                )}
                <p className="mb-4 inline-flex rounded-full border border-academy/30 bg-academy-soft px-4 py-1.5 text-sm font-bold text-academy">
                  Frais : {priceLabel}
                </p>

                {isPreRegistration && session.registration_opens_at && (
                  <div className="mb-4">
                    <RegistrationOpensCountdown
                      targetIso={session.registration_opens_at}
                      targetLabel={opensLabel}
                      variant="compact"
                    />
                  </div>
                )}

                {showPromoMessage && (
                  isPreRegistration && session.pre_registration_message ? (
                    <div className="mb-4 text-sm text-muted leading-relaxed line-clamp-4 md:text-base">
                      <RichHtmlContent html={session.pre_registration_message} />
                    </div>
                  ) : (
                    <p className="mb-4 text-sm text-muted leading-relaxed line-clamp-3 md:text-base">
                      {promoExcerpt}
                    </p>
                  )
                )}

                <SessionRegistrationBenefits benefits={benefits} variant="modal" />

                <div className="mt-auto flex flex-wrap gap-3 pt-1">
                  {hasVideo && (
                    <button type="button" className="btn btn-outline" onClick={openModal}>
                      Voir le spot
                    </button>
                  )}
                  <Link
                    href={ctaHref}
                    className="btn btn-gold btn-lg"
                    onClick={closeModal}
                  >
                    {ctaLabel}
                  </Link>
                  <button type="button" className="btn btn-outline" onClick={closeModal}>
                    Plus tard
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      )}

      {hasVideo && <SpotVideoModal />}

      {fabVisible && !modalOpen && (
        <button
          type="button"
          className={`session-fab${isRegistrationPage ? " session-fab--registration" : ""}`}
          onClick={() => setModalOpen(true)}
          aria-label={fabAriaLabel}
        >
          <span className="session-fab-dot" aria-hidden />
          {fabLabel}
        </button>
      )}
    </>
  );
}

/**
 * Modale d'accueil pour la session ouverte ou en pré-inscription + bouton flottant de rappel.
 */
export function ActiveSessionPromo({ session: initialSession }: ActiveSessionPromoProps) {
  const pathname = usePathname();
  const [session, setSession] = useState<TrainingSession | null>(initialSession);

  useEffect(() => {
    setSession(initialSession);
  }, [initialSession]);

  useEffect(() => {
    if (session && sessionShowsPromo(session)) {
      return;
    }

    let cancelled = false;

    fetch(`${API_BASE}/academy/sessions?open_only=1`, {
      headers: { Accept: "application/json" },
    })
      .then((response) => (response.ok ? response.json() : null))
      .then((json: ApiResponse<TrainingSession[]> | null) => {
        if (cancelled || !json?.data?.length) {
          return;
        }

        const primary = pickPrimarySession(json.data);

        if (primary && sessionShowsPromo(primary)) {
          setSession(primary);
        }
      })
      .catch(() => {});

    return () => {
      cancelled = true;
    };
  }, [session?.status, session?.shows_pre_registration_page]);

  if (!session || !sessionShowsPromo(session) || isPromoHiddenPath(pathname)) {
    return null;
  }

  return <ActiveSessionPromoContent session={session} />;
}
