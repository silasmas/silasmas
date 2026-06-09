"use client";

import Link from "next/link";
import { usePathname } from "next/navigation";
import { useEffect, useState } from "react";
import { SessionPoster } from "@/components/academy/SessionPoster";
import { SessionRegistrationBenefits } from "@/components/academy/SessionRegistrationBenefits";
import { useSpotVideoModal } from "@/components/academy/SpotVideoModal";
import { useRegistrationBenefits } from "@/hooks/useRegistrationBenefits";
import type { TrainingSession } from "@/types/api";

interface ActiveSessionPromoProps {
  session: TrainingSession | null;
}

const DISMISS_PREFIX = "sdev-academy-promo-dismissed-";

function ActiveSessionPromoContent({ session }: { session: TrainingSession }) {
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
                <SessionPoster session={session} priority variant="modal" />
              </div>
              <div className="session-modal-content">
                <p className="section-eyebrow mb-2">Session en cours — SDev Academy</p>
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
                {session.description && (
                  <p className="mb-4 text-sm text-muted leading-relaxed line-clamp-2">
                    {session.description}
                  </p>
                )}
                <SessionRegistrationBenefits benefits={benefits} variant="modal" />
                <div className="mt-auto flex flex-wrap gap-3 pt-1">
                  {hasVideo && (
                    <button type="button" className="btn btn-outline" onClick={openModal}>
                      Voir le spot
                    </button>
                  )}
                  <Link
                    href={`/academy/${session.slug}#inscription`}
                    className="btn btn-gold btn-lg"
                    onClick={closeModal}
                  >
                    S&apos;inscrire maintenant
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
          className="session-fab"
          onClick={() => setModalOpen(true)}
          aria-label="Voir la session Academy en cours"
        >
          <span className="session-fab-dot" aria-hidden />
          Session Academy
        </button>
      )}
    </>
  );
}

/**
 * Modale d'accueil pour la session ouverte + bouton flottant de rappel.
 */
export function ActiveSessionPromo({ session }: ActiveSessionPromoProps) {
  const pathname = usePathname();

  if (!session || session.status !== "open" || pathname.startsWith("/academy/")) {
    return null;
  }

  return <ActiveSessionPromoContent session={session} />;
}
