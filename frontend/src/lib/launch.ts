import { nav } from "@/lib/site";
import type { TrainingSession } from "@/types/api";

/** Active le mode lancement : uniquement la session Academy visible. */
export const ACADEMY_LAUNCH_MODE =
  process.env.NEXT_PUBLIC_ACADEMY_LAUNCH_MODE !== "false";

/** Slug de repli si l'API ne renvoie aucune session ouverte. */
export const PRIMARY_SESSION_SLUG =
  process.env.NEXT_PUBLIC_PRIMARY_SESSION_SLUG ?? "vibe-coding-2026";

/** Routes masquées (redirection vers la session active). */
export const LAUNCH_HIDDEN_PATHS = [
  "/silas",
  "/studio",
  "/portfolio",
  "/contact",
] as const;

/**
 * Indique si le site est en mode lancement Academy uniquement.
 *
 * @return true si les pages secondaires sont masquées
 */
export function isAcademyLaunchMode(): boolean {
  return ACADEMY_LAUNCH_MODE;
}

/**
 * Construit le chemin vers une session Academy.
 *
 * @param slug Slug de session
 * @return Chemin /academy/{slug}
 */
export function buildAcademySessionPath(slug: string): string {
  return `/academy/${slug}`;
}

/**
 * Résout le slug de la session mise en avant depuis l'API.
 *
 * @param sessions Sessions ouvertes
 * @return Slug API ou repli configuré
 */
export function resolvePrimarySessionSlug(
  sessions: TrainingSession[],
): string {
  const featured = sessions.find((session) => session.is_featured);

  if (featured?.slug) {
    return featured.slug;
  }

  if (sessions[0]?.slug) {
    return sessions[0].slug;
  }

  return PRIMARY_SESSION_SLUG;
}

/**
 * Retourne les liens de navigation selon le mode actif.
 *
 * @param sessionSlug Slug résolu depuis l'API (optionnel)
 * @return Liste des entrées de menu
 */
export function getVisibleNav(sessionSlug?: string | null) {
  if (!isAcademyLaunchMode()) {
    return nav;
  }

  const slug = sessionSlug ?? PRIMARY_SESSION_SLUG;

  return [
    {
      href: buildAcademySessionPath(slug),
      label: "SDev Academy",
    },
  ] as const;
}

/**
 * URL d'inscription principale (session active ou repli).
 *
 * @param sessionSlug Slug résolu depuis l'API (optionnel)
 * @return Chemin relatif vers l'inscription
 */
export function getPrimaryRegistrationHref(sessionSlug?: string | null): string {
  const slug = sessionSlug ?? PRIMARY_SESSION_SLUG;

  return buildAcademySessionPath(slug);
}
