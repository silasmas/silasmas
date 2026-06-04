"use client";

import { useCallback, useState, type ReactElement } from "react";
import type { TrainingSession } from "@/types/api";

interface SpotVideoModalProps {
  session: TrainingSession;
  open: boolean;
  onClose: () => void;
}

/**
 * Modale de lecture de la vidéo spot (chargement iframe au clic pour YouTube).
 */
export function SpotVideoModal({ session, open, onClose }: SpotVideoModalProps) {
  const [iframeReady, setIframeReady] = useState(false);

  if (!open || !session.spot_video_embed_url) {
    return null;
  }

  const isYoutube = session.spot_video_type === "youtube";
  const isVimeo = session.spot_video_type === "vimeo";
  const isEmbed = isYoutube || isVimeo;
  const thumbnail =
    session.spot_video_thumbnail_url ?? session.cover_image_url ?? session.cover_image;
  const watchUrl = session.spot_video_watch_url ?? session.spot_video_url;

  const handleClose = () => {
    setIframeReady(false);
    onClose();
  };

  const startPlayback = () => {
    setIframeReady(true);
  };

  return (
    <div className="session-modal-overlay session-video-overlay" role="dialog" aria-modal="true">
      <div className="session-modal session-video-modal max-w-4xl">
        <button
          type="button"
          className="session-modal-close"
          onClick={handleClose}
          aria-label="Fermer la vidéo"
        >
          ×
        </button>

        <div className="session-video-modal-header">
          <h2 className="text-xl font-bold">{session.title}</h2>
          <p className="text-sm text-muted">Spot vidéo</p>
        </div>

        <div className="session-video-player">
          {isEmbed && !iframeReady ? (
            <button
              type="button"
              className="session-video-facade"
              onClick={startPlayback}
              aria-label="Lancer la lecture"
            >
              {thumbnail && (
                // eslint-disable-next-line @next/next/no-img-element
                <img src={thumbnail} alt="" className="session-video-facade-thumb" />
              )}
              <span className="session-video-facade-play">▶ Lire la vidéo</span>
            </button>
          ) : isEmbed ? (
            <iframe
              src={`${session.spot_video_embed_url}${session.spot_video_embed_url?.includes("?") ? "&" : "?"}autoplay=1`}
              title={`Spot — ${session.title}`}
              className="h-full w-full"
              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
              referrerPolicy="strict-origin-when-cross-origin"
              allowFullScreen
            />
          ) : (
            <video
              src={session.spot_video_embed_url}
              controls
              autoPlay
              className="h-full w-full"
            />
          )}
        </div>

        {isYoutube && watchUrl && (
          <p className="session-video-fallback">
            Problème de lecture ?{" "}
            <a href={watchUrl} target="_blank" rel="noopener noreferrer" className="text-accent hover:underline">
              Ouvrir sur YouTube
            </a>
          </p>
        )}
      </div>
    </div>
  );
}

interface UseSpotVideoModalResult {
  open: boolean;
  openModal: () => void;
  closeModal: () => void;
  SpotVideoModal: () => ReactElement | null;
}

/**
 * Hook pour ouvrir/fermer la modale vidéo spot.
 */
export function useSpotVideoModal(session: TrainingSession): UseSpotVideoModalResult {
  const [open, setOpen] = useState(false);

  const openModal = useCallback(() => {
    setOpen(true);
  }, []);

  const closeModal = useCallback(() => {
    setOpen(false);
  }, []);

  const Modal = useCallback(() => {
    return <SpotVideoModal session={session} open={open} onClose={closeModal} />;
  }, [session, open, closeModal]);

  return {
    open,
    openModal,
    closeModal,
    SpotVideoModal: Modal,
  };
}
