import { nav } from "@/lib/site";

/** Active le mode lancement : uniquement la session Academy visible. */
export const ACADEMY_LAUNCH_MODE =
  process.env.NEXT_PUBLIC_ACADEMY_LAUNCH_MODE !== "false";

/** Slug de la session mise en avant (inscription + paiement). */
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

/** Navigation réduite pour le mode lancement. */
export const launchNav = [
  {
    href: `/academy/${PRIMARY_SESSION_SLUG}`,
    label: "SDev Academy",
  },
] as const;

/**
 * Retourne les liens de navigation selon le mode actif.
 *
 * @return Liste des entrées de menu
 */
export function getVisibleNav() {
  return isAcademyLaunchMode() ? launchNav : nav;
}

/**
 * URL d'inscription principale (session active ou liste Academy).
 *
 * @return Chemin relatif vers l'inscription
 */
export function getPrimaryRegistrationHref(): string {
  return `/academy/${PRIMARY_SESSION_SLUG}`;
}
