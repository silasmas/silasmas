import { resolveStorageUrl } from "@/lib/api";
import type { Project } from "@/types/api";

const SDEV_LOGO = "/images/logo.png";

/**
 * URL du logo projet avec repli SDev si absent ou invalide.
 */
export function projectLogoUrl(project: Project): string {
  const resolved = resolveStorageUrl(project.logo_url);

  if (resolved && resolved.length > 0 && !resolved.includes("undefined")) {
    return resolved;
  }

  return SDEV_LOGO;
}

/**
 * Indique si le logo projet est le repli SDev.
 */
export function isFallbackProjectLogo(project: Project): boolean {
  return projectLogoUrl(project) === SDEV_LOGO;
}
