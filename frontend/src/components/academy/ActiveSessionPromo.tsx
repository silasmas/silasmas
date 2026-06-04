"use client";

import Link from "next/link";
import { usePathname } from "next/navigation";
import { useEffect, useState } from "react";
import { SessionMediaPanel } from "@/components/academy/SessionMediaPanel";
import { SocialShareButtons } from "@/components/academy/SocialShareButtons";
import { useSpotVideoModal } from "@/components/academy/SpotVideoModal";
import { getSessionShareUrl } from "@/lib/share";
import type { TrainingSession } from "@/types/api";

interface ActiveSessionPromoProps {
  session: TrainingSession | null;
}

const DISMISS_PREFIX = "sdev-academy-promo-dismissed-";

function formatDate(date: string): string {
  return new Date(date).toLocaleDateString("fr-FR", {
    day: "numeric",
    month: "long",
    year: "numeric",
  });
}

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
  const shareUrl = getSessionShareUrl(session);

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
          <div className="session-modal">
            <button
              type="button"
              className="session-modal-close"
              onClick={closeModal}
              aria-label="Fermer"
            >
              ×
            </button>

            <div className="session-modal-grid">
              <SessionMediaPanel session={session} priority showShare={false} compact />
              <div>
                <p className="section-eyebrow mb-3">Session en cours</p>
                <h2 id="session-promo-title" className="mb-3 text-2xl font-bold">
                  {session.title}
                </h2>
                {session.subtitle && <p className="mb-3 text-muted">{session.subtitle}</p>}
                <p className="mb-4 text-sm text-accent">
                  {formatDate(session.start_date)} — {formatDate(session.end_date)}
                </p>
                {session.description && (
                  <p className="mb-6 text-sm text-muted line-clamp-4">{session.description}</p>
                )}
                <SocialShareButtons
                  url={shareUrl}
                  title={session.title}
                  className="mb-6"
                />
                <div className="flex flex-wrap gap-3">
                  {hasVideo && (
                    <button type="button" className="btn btn-outline" onClick={openModal}>
                      Voir le spot
                    </button>
                  )}
                  <Link href={`/academy/${session.slug}`} className="btn btn-gold" onClick={closeModal}>
                    S&apos;inscrire
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
