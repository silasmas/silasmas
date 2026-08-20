import Script from "next/script";
import { ADSENSE_CLIENT_ID } from "@/lib/google-adsense";

/**
 * Charge le script Google AdSense sur toutes les pages du site.
 */
export function GoogleAdSense() {
  if (!ADSENSE_CLIENT_ID) {
    return null;
  }

  return (
    <Script
      id="google-adsense"
      async
      src={`https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=${ADSENSE_CLIENT_ID}`}
      crossOrigin="anonymous"
      strategy="afterInteractive"
    />
  );
}
