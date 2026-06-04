"use client";

import { sessionCoverUrl } from "@/lib/api";
import { getSessionShareUrl } from "@/lib/share";
import type { TrainingSession } from "@/types/api";
import { SessionPoster } from "@/components/academy/SessionPoster";
import { SocialShareButtons } from "@/components/academy/SocialShareButtons";
import { useSpotVideoModal } from "@/components/academy/SpotVideoModal";

interface SessionMediaPanelProps {
  session: TrainingSession;
  priority?: boolean;
  className?: string;
  showShare?: boolean;
  /** Mode compact : pas de bouton secondaire sous l'affiche (modale promo). */
  compact?: boolean;
}

/**
 * Affiche l'affiche, le bouton vidéo spot et le partage RS.
 */
export function SessionMediaPanel({
  session,
  priority = false,
  className = "",
  showShare = true,
  compact = false,
}: SessionMediaPanelProps) {
  const hasVideo =
    session.spot_video_type !== "none" &&
    Boolean(session.spot_video_embed_url || session.spot_video_url);
  const { openModal, SpotVideoModal } = useSpotVideoModal(session);
  const shareUrl = getSessionShareUrl(session);

  return (
    <div className={`session-media-panel space-y-4 ${className}`}>
      <div className="session-media-poster-wrap relative">
        <SessionPoster
          session={session}
          className="aspect-[3/4] min-h-[240px] w-full"
          priority={priority}
        />
        {hasVideo && (
          <button
            type="button"
            className="session-play-btn"
            onClick={openModal}
            aria-label="Lire la vidéo spot"
          >
            ▶ Spot vidéo
          </button>
        )}
      </div>

      {hasVideo && !compact && (
        <button type="button" className="btn btn-outline w-full sm:w-auto" onClick={openModal}>
          Voir le spot vidéo
        </button>
      )}

      {showShare && (
        <SocialShareButtons
          url={shareUrl}
          title={session.title}
          text={
            session.subtitle ??
            `Inscrivez-vous à la session SDev Academy : ${session.title}`
          }
        />
      )}

      {!sessionCoverUrl(session) && hasVideo && !compact && (
        <p className="text-xs text-muted">
          Ajoutez une affiche dans l&apos;admin pour un meilleur rendu visuel.
        </p>
      )}

      <SpotVideoModal />
    </div>
  );
}
