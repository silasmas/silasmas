import type { Metadata } from "next";

/** Chemin de base des favicons statiques du site. */
export const DEFAULT_FAVICON_BASE = "/assets/img/favicon";

/** Jeu complet de favicons par défaut (dossier public/assets/img/favicon). */
export const DEFAULT_FAVICON_ICONS: NonNullable<Metadata["icons"]> = {
  icon: [
    {
      url: `${DEFAULT_FAVICON_BASE}/favicon-32x32.png`,
      sizes: "32x32",
      type: "image/png",
    },
    {
      url: `${DEFAULT_FAVICON_BASE}/favicon-16x16.png`,
      sizes: "16x16",
      type: "image/png",
    },
  ],
  apple: `${DEFAULT_FAVICON_BASE}/apple-touch-icon.png`,
  shortcut: `${DEFAULT_FAVICON_BASE}/favicon.ico`,
};

/**
 * URL principale du favicon (configurée via l'API ou 32×32 par défaut).
 *
 * @param faviconUrl URL renvoyée par l'API (déjà résolue côté Laravel)
 * @returns Chemin ou URL du favicon
 */
export function siteFaviconUrl(faviconUrl: string | null | undefined): string {
  return faviconUrl ?? `${DEFAULT_FAVICON_BASE}/favicon-32x32.png`;
}

/**
 * Métadonnées Next.js : favicon admin si présent, sinon jeu par défaut.
 *
 * @param faviconUrl URL renvoyée par l'API
 * @returns Objet icons pour generateMetadata
 */
export function resolveSiteFaviconIcons(
  faviconUrl: string | null | undefined
): NonNullable<Metadata["icons"]> {
  if (faviconUrl) {
    return { icon: faviconUrl };
  }

  return DEFAULT_FAVICON_ICONS;
}
