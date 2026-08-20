import type { Metadata } from "next";
import { Geist, Geist_Mono, Instrument_Serif } from "next/font/google";
import Script from "next/script";
import { ActiveSessionPromoLazy } from "@/components/academy/ActiveSessionPromoLazy";
import { FacebookPixel } from "@/components/analytics/FacebookPixel";
import { SiteAnalyticsTracker } from "@/components/analytics/SiteAnalyticsTracker";
import { ADSENSE_CLIENT_ID } from "@/lib/google-adsense";
import { JsonLd } from "@/components/seo/JsonLd";
import { Navbar } from "@/components/site/Navbar";
import { Footer } from "@/components/site/Footer";
import { SiteSettingsProvider } from "@/components/providers/SiteSettingsProvider";
import { ThemeProvider } from "@/components/providers/ThemeProvider";
import { FALLBACK_SITE_SETTINGS } from "@/data/site-fallbacks";
import { getOpenSessions, getSiteContent } from "@/lib/api";
import {
  getPrimaryRegistrationHref,
  getVisibleNav,
  isAcademyLaunchMode,
  resolvePrimarySessionSlug,
} from "@/lib/launch";
import { pickPrimarySession } from "@/lib/sessions";
import { resolveSiteFaviconIcons } from "@/lib/site-favicon";
import { buildOrganizationJsonLd, buildPageMetadata } from "@/lib/seo";
import { site } from "@/lib/site";
import "./globals.css";

const themeBootScript = `(() => {
  try {
    var k = 'sd-theme';
    var s = localStorage.getItem(k);
    var t = (s === 'light' || s === 'dark')
      ? s
      : (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
    document.documentElement.classList.toggle('dark', t === 'dark');
    document.documentElement.setAttribute('data-theme', t);
    document.documentElement.style.colorScheme = t;
  } catch (e) {}
})();`;

const geistSans = Geist({
  variable: "--font-geist-sans",
  subsets: ["latin"],
  display: "swap",
});

const geistMono = Geist_Mono({
  variable: "--font-geist-mono",
  subsets: ["latin"],
  display: "swap",
});

const instrumentSerif = Instrument_Serif({
  variable: "--font-instrument-serif",
  subsets: ["latin"],
  weight: "400",
  style: ["normal", "italic"],
  display: "swap",
});

/**
 * Métadonnées dynamiques depuis l'API paramétrage.
 */
export async function generateMetadata(): Promise<Metadata> {
  const siteContent = await getSiteContent();
  const settings = siteContent?.settings ?? FALLBACK_SITE_SETTINGS;

  const title = `${settings.site_title} — Studio numérique & SDev Academy`;
  const description = settings.footer_description ?? site.description;

  return {
    ...buildPageMetadata({
      title,
      description,
      path: "/",
    }),
    metadataBase: new URL(process.env.NEXT_PUBLIC_SITE_URL ?? site.url),
    title: {
      default: title,
      template: `%s — ${settings.site_title}`,
    },
    icons: resolveSiteFaviconIcons(settings.favicon_url),
  };
}

/**
 * Layout racine du site public Next.js (design sdev-platform).
 */
export default async function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  const [openSessions, siteContent] = await Promise.all([
    getOpenSessions(),
    getSiteContent(),
  ]);
  const primarySession = pickPrimarySession(openSessions);
  const primarySlug = resolvePrimarySessionSlug(openSessions);
  const settings = siteContent?.settings ?? FALLBACK_SITE_SETTINGS;
  const launchMode = isAcademyLaunchMode();
  const navItems = getVisibleNav(primarySlug).map((item) => ({
    href: item.href,
    label: item.label,
  }));
  const ctaHref = launchMode
    ? `${getPrimaryRegistrationHref(primarySlug)}#inscription`
    : "/contact";
  const ctaLabel = launchMode ? "S'inscrire" : "Démarrer un projet";
  const jsonLd = buildOrganizationJsonLd(
    settings.site_title ?? site.name,
    settings.footer_description ?? site.description
  );

  return (
    <html
      lang="fr"
      suppressHydrationWarning
      className={`${geistSans.variable} ${geistMono.variable} ${instrumentSerif.variable} h-full antialiased`}
      style={
        {
          ["--font-sans-stack" as string]:
            "var(--font-geist-sans), ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, sans-serif",
          ["--font-mono-stack" as string]:
            "var(--font-geist-mono), ui-monospace, SFMono-Regular, Menlo, monospace",
          ["--font-display-stack" as string]:
            "var(--font-instrument-serif), 'Times New Roman', serif",
        } as React.CSSProperties
      }
    >
      <head>
        {/* Google AdSense — requis dans <head> sur toutes les pages */}
        <script
          async
          src={`https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=${ADSENSE_CLIENT_ID}`}
          crossOrigin="anonymous"
        />
      </head>
      <body className="min-h-full flex flex-col bg-bg text-ink">
        <JsonLd data={jsonLd} />
        <Script
          id="sd-theme-boot"
          strategy="beforeInteractive"
          dangerouslySetInnerHTML={{ __html: themeBootScript }}
        />
        <SiteSettingsProvider settings={settings}>
          <ThemeProvider>
            <Navbar
              items={navItems}
              ctaHref={ctaHref}
              ctaLabel={ctaLabel}
            />
            <main className="flex-1">{children}</main>
            <Footer
              registrationHref={
                launchMode ? getPrimaryRegistrationHref(primarySlug) : undefined
              }
            />
            <ActiveSessionPromoLazy session={primarySession} />
            <SiteAnalyticsTracker />
            <FacebookPixel />
          </ThemeProvider>
        </SiteSettingsProvider>
      </body>
    </html>
  );
}
