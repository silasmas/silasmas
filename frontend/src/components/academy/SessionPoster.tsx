import { sessionCoverUrl } from "@/lib/api";
import type { TrainingSession } from "@/types/api";

interface SessionPosterProps {
  session: TrainingSession;
  className?: string;
  priority?: boolean;
}

/**
 * Affiche l'affiche d'une session ou un visuel de repli.
 */
export function SessionPoster({
  session,
  className = "",
  priority = false,
}: SessionPosterProps) {
  const imageUrl = sessionCoverUrl(session);

  if (imageUrl) {
    return (
      <div
        className={`relative aspect-[3/4] w-full overflow-hidden rounded-2xl border border-[var(--color-border)] bg-[var(--color-bg-card)] ${className}`}
      >
        {/* eslint-disable-next-line @next/next/no-img-element */}
        <img
          src={imageUrl}
          alt={`Affiche — ${session.title}`}
          className="absolute inset-0 h-full w-full object-cover"
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
