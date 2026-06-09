import { sessionCoverUrl } from "@/lib/api";
import type { TrainingSession } from "@/types/api";

interface SessionPosterProps {
  session: TrainingSession;
  className?: string;
  priority?: boolean;
  /** hero : page session · modal : modale promo sans débordement. */
  variant?: "default" | "hero" | "modal";
}

/**
 * Affiche l'affiche d'une session ou un visuel de repli.
 */
export function SessionPoster({
  session,
  className = "",
  priority = false,
  variant = "default",
}: SessionPosterProps) {
  const imageUrl = sessionCoverUrl(session);
  const aspectClass =
    variant === "modal"
      ? "session-poster-modal aspect-[2/3] h-full max-h-[min(70vh,500px)] w-auto"
      : variant === "hero"
        ? "aspect-[2/3] min-h-[320px] md:min-h-[420px]"
        : "aspect-[3/4]";

  if (imageUrl) {
    return (
      <div
        className={`relative ${aspectClass} overflow-hidden rounded-2xl border border-line bg-[#001f3f] ${variant === "modal" ? "mx-auto" : "w-full"} ${className}`}
      >
        {/* eslint-disable-next-line @next/next/no-img-element */}
        <img
          src={imageUrl}
          alt={`Affiche — ${session.title}`}
          className="absolute inset-0 h-full w-full object-contain"
          loading={priority ? "eager" : "lazy"}
        />
      </div>
    );
  }

  return (
    <div
      className={`flex aspect-[3/4] min-h-[220px] w-full flex-col items-center justify-center rounded-2xl border border-[var(--color-border)] bg-gradient-to-br from-amber-500/15 to-orange-600/10 p-8 text-center ${className}`}
    >
      <p className="text-5xl font-black text-gold">SD</p>
      <p className="mt-2 text-lg font-semibold text-[var(--color-text)]">Academy</p>
      <p className="mt-4 text-sm text-muted">Session de formation</p>
    </div>
  );
}
