/** Identifiant Meta Pixel (configurable via NEXT_PUBLIC_FACEBOOK_PIXEL_ID). */
export const FACEBOOK_PIXEL_ID =
  process.env.NEXT_PUBLIC_FACEBOOK_PIXEL_ID ?? "1534882331705238";

declare global {
  interface Window {
    fbq?: (...args: unknown[]) => void;
    _fbq?: unknown;
  }
}

/**
 * Envoie un événement PageView à Meta Pixel si le script est chargé.
 */
export function trackFacebookPageView(): void {
  if (typeof window === "undefined" || typeof window.fbq !== "function") {
    return;
  }

  window.fbq("track", "PageView");
}
