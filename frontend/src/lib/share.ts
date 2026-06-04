import type { TrainingSession } from "@/types/api";

const SITE_URL = process.env.NEXT_PUBLIC_SITE_URL ?? "http://localhost:3000";

/**
 * URL publique de partage d'une session Academy.
 */
export function getSessionShareUrl(session: TrainingSession): string {
  if (session.share_url) {
    return session.share_url;
  }

  return `${SITE_URL.replace(/\/$/, "")}/academy/${session.slug}`;
}
