import { siteFaviconResponse } from "@/lib/site-favicon-server";

export const size = { width: 180, height: 180 };
export const contentType = "image/png";
export const revalidate = 3600;

/**
 * Icône Apple Touch : favicon admin si configuré, sinon apple-touch-icon par défaut.
 */
export default function AppleIcon() {
  return siteFaviconResponse("apple");
}
