"use client";

import { createContext, useContext, type ReactNode } from "react";
import type { SiteSettings } from "@/types/api";
import { FALLBACK_SITE_SETTINGS } from "@/data/site-fallbacks";
import { siteFaviconUrl as resolveSiteFaviconUrl } from "@/lib/site-favicon";

const SiteSettingsContext = createContext<SiteSettings>(FALLBACK_SITE_SETTINGS);

/**
 * Fournit les paramètres globaux du site au layout.
 */
export function SiteSettingsProvider({
  settings,
  children,
}: {
  settings: SiteSettings;
  children: ReactNode;
}) {
  return (
    <SiteSettingsContext.Provider value={settings}>
      {children}
    </SiteSettingsContext.Provider>
  );
}

/**
 * Accès aux paramètres du site (logo, contact, titre…).
 */
export function useSiteSettings(): SiteSettings {
  return useContext(SiteSettingsContext);
}

/**
 * URL du logo site avec repli local.
 */
export function siteLogoUrl(settings: SiteSettings): string {
  return settings.logo_url ?? "/images/logo.png";
}

/**
 * URL du favicon site avec repli vers le jeu par défaut.
 */
export function siteFaviconUrl(settings: SiteSettings): string {
  return resolveSiteFaviconUrl(settings.favicon_url);
}
