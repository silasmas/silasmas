const API_BASE =
  process.env.NEXT_PUBLIC_API_URL ?? "http://localhost:8000/api";

const VISITOR_KEY = "sd-visitor-key";

export interface AnalyticsEventPayload {
  event_type: "page_view" | "click";
  path: string;
  page_title?: string;
  click_label?: string;
  click_target?: string;
  visitor_key?: string;
  referrer?: string;
  visited_at?: string;
}

/**
 * Retourne ou crée un identifiant anonyme de visiteur (localStorage).
 */
export function getVisitorKey(): string {
  if (typeof window === "undefined") {
    return "";
  }

  const existing = window.localStorage.getItem(VISITOR_KEY);

  if (existing) {
    return existing;
  }

  const key = crypto.randomUUID();
  window.localStorage.setItem(VISITOR_KEY, key);

  return key;
}

/**
 * Envoie un événement analytics à l'API Laravel (fire-and-forget).
 */
export async function trackAnalyticsEvent(payload: AnalyticsEventPayload): Promise<void> {
  try {
    await fetch(`${API_BASE}/analytics/track`, {
      method: "POST",
      headers: {
        Accept: "application/json",
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        ...payload,
        visitor_key: payload.visitor_key ?? getVisitorKey(),
        referrer: payload.referrer ?? (typeof document !== "undefined" ? document.referrer : ""),
        visited_at: payload.visited_at ?? new Date().toISOString(),
      }),
      keepalive: true,
    });
  } catch {
    // Ne pas bloquer l'UX si l'API est indisponible
  }
}

/**
 * Enregistre une page vue.
 */
export function trackPageView(path: string, pageTitle?: string): void {
  void trackAnalyticsEvent({
    event_type: "page_view",
    path,
    page_title: pageTitle,
  });
}

/**
 * Enregistre un clic sur un élément interactif.
 */
export function trackClick(
  path: string,
  clickLabel: string,
  clickTarget?: string,
  pageTitle?: string
): void {
  void trackAnalyticsEvent({
    event_type: "click",
    path,
    page_title: pageTitle,
    click_label: clickLabel,
    click_target: clickTarget,
  });
}

/**
 * Détermine si un clic doit être enregistré.
 */
export function shouldTrackClick(element: Element): boolean {
  if (element.closest("[data-analytics-ignore]")) {
    return false;
  }

  if (element.hasAttribute("data-track-click")) {
    return true;
  }

  if (element.matches(".btn-gold, .btn-primary, .session-fab, [href*='#inscription'], [href*='/academy/']")) {
    return true;
  }

  return element.tagName === "A" || element.tagName === "BUTTON";
}

/**
 * Extrait un libellé lisible depuis un élément cliqué.
 */
export function resolveClickLabel(element: Element): string {
  const explicit = element.getAttribute("data-track-click");

  if (explicit) {
    return explicit.trim();
  }

  if (element instanceof HTMLAnchorElement && element.href) {
    return element.textContent?.trim() || element.href;
  }

  const text = element.textContent?.trim();

  if (text) {
    return text.slice(0, 120);
  }

  return element.getAttribute("aria-label") ?? "Clic";
}

/**
 * Extrait la cible d'un clic (href ou id).
 */
export function resolveClickTarget(element: Element): string | undefined {
  if (element instanceof HTMLAnchorElement) {
    return element.href;
  }

  return element.getAttribute("href") ?? element.id ?? undefined;
}
