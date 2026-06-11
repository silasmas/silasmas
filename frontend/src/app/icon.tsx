import { siteFaviconResponse } from "@/lib/site-favicon-server";

export const size = { width: 32, height: 32 };
export const contentType = "image/png";
export const revalidate = 3600;

/**
 * Icône d'onglet Next.js : favicon admin si configuré, sinon favicon par défaut.
 */
export default function Icon() {
  return siteFaviconResponse("icon");
}
