"use client";

import { usePathname } from "next/navigation";
import { useEffect } from "react";
import {
  resolveClickLabel,
  resolveClickTarget,
  shouldTrackClick,
  trackClick,
  trackPageView,
} from "@/lib/analytics";

/**
 * Envoie les pages vues et les clics importants vers l'API analytics.
 */
export function SiteAnalyticsTracker() {
  const pathname = usePathname();

  useEffect(() => {
    trackPageView(pathname, document.title);
  }, [pathname]);

  useEffect(() => {
    /**
     * Capture les clics sur liens et boutons suivis.
     */
    function handleClick(event: MouseEvent) {
      const target = event.target;

      if (!(target instanceof Element)) {
        return;
      }

      const interactive = target.closest("a, button, [data-track-click]");

      if (!(interactive instanceof Element) || !shouldTrackClick(interactive)) {
        return;
      }

      trackClick(
        pathname,
        resolveClickLabel(interactive),
        resolveClickTarget(interactive),
        document.title
      );
    }

    document.addEventListener("click", handleClick, true);

    return () => {
      document.removeEventListener("click", handleClick, true);
    };
  }, [pathname]);

  return null;
}
