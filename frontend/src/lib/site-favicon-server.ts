import "server-only";

import { readFile } from "node:fs/promises";
import path from "node:path";
import { FALLBACK_SITE_SETTINGS } from "@/data/site-fallbacks";
import { getSiteContent } from "@/lib/api";
import { DEFAULT_FAVICON_BASE } from "@/lib/site-favicon";

const REVALIDATE_SECONDS = 3600;

type FaviconVariant = "icon" | "apple";

/**
 * Charge le binaire du favicon effectif (configuré via l'API ou fichier par défaut).
 *
 * @param variant Variante « icon » (32×32) ou « apple » (180×180)
 * @returns Buffer image et type MIME
 */
export async function loadSiteFaviconBytes(
  variant: FaviconVariant = "icon"
): Promise<{ buffer: Buffer; contentType: string }> {
  const siteContent = await getSiteContent();
  const settings = siteContent?.settings ?? FALLBACK_SITE_SETTINGS;

  if (settings.favicon_url) {
    const remote = await fetch(settings.favicon_url, {
      next: { revalidate: REVALIDATE_SECONDS },
    });

    if (remote.ok) {
      const arrayBuffer = await remote.arrayBuffer();

      return {
        buffer: Buffer.from(arrayBuffer),
        contentType: remote.headers.get("content-type") ?? "image/png",
      };
    }
  }

  const defaultFile =
    variant === "apple"
      ? `${DEFAULT_FAVICON_BASE}/apple-touch-icon.png`
      : `${DEFAULT_FAVICON_BASE}/favicon-32x32.png`;

  const publicPath = path.join(process.cwd(), "public", defaultFile.replace(/^\//, ""));
  const buffer = await readFile(publicPath);

  return {
    buffer,
    contentType: defaultFile.endsWith(".ico") ? "image/x-icon" : "image/png",
  };
}

/**
 * Réponse HTTP prête pour les routes metadata Next.js (`icon`, `apple-icon`).
 *
 * @param variant Variante d'icône
 * @returns Response avec le binaire image
 */
export async function siteFaviconResponse(variant: FaviconVariant = "icon"): Promise<Response> {
  const { buffer, contentType } = await loadSiteFaviconBytes(variant);

  return new Response(new Uint8Array(buffer), {
    headers: { "Content-Type": contentType },
  });
}
