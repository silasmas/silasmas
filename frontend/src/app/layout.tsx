import type { Metadata } from "next";
import { Inter, Plus_Jakarta_Sans } from "next/font/google";
import { ActiveSessionPromo } from "@/components/academy/ActiveSessionPromo";
import { Footer } from "@/components/layout/Footer";
import { Header } from "@/components/layout/Header";
import { SiteSettingsProvider } from "@/components/providers/SiteSettingsProvider";
import { ThemeProvider } from "@/components/providers/ThemeProvider";
import { FALLBACK_SITE_SETTINGS } from "@/data/site-fallbacks";
import { getOpenSessions, getSiteContent } from "@/lib/api";
import { pickPrimarySession } from "@/lib/sessions";
import "./globals.css";

const displayFont = Plus_Jakarta_Sans({
  variable: "--font-display",
  subsets: ["latin"],
  weight: ["500", "600", "700", "800"],
});

const bodyFont = Inter({
  variable: "--font-body",
  subsets: ["latin"],
  weight: ["400", "500", "600", "700"],
});

/**
 * Métadonnées dynamiques depuis l'API paramétrage.
 */
export async function generateMetadata(): Promise<Metadata> {
  const site = await getSiteContent();
  const settings = site?.settings ?? FALLBACK_SITE_SETTINGS;
  const title = `${settings.site_title} | ${settings.site_tagline ?? "SDEV"}`;

  return {
    title,
    description: settings.footer_description ?? undefined,
    icons: settings.favicon_url ? { icon: settings.favicon_url } : { icon: "/images/logo.png" },
    metadataBase: new URL(process.env.NEXT_PUBLIC_SITE_URL ?? "http://localhost:3000"),
  };
}

/**
 * Layout racine du site public Next.js.
 */
export default async function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  const [openSessions, site] = await Promise.all([getOpenSessions(), getSiteContent()]);
  const primarySession = pickPrimarySession(openSessions);
  const settings = site?.settings ?? FALLBACK_SITE_SETTINGS;

  return (
    <html
      lang="fr"
      data-theme="light"
      className={`${displayFont.variable} ${bodyFont.variable}`}
      suppressHydrationWarning
    >
      <body>
        <SiteSettingsProvider settings={settings}>
          <ThemeProvider>
            <Header />
            <main>{children}</main>
            <Footer />
            <ActiveSessionPromo session={primarySession} />
          </ThemeProvider>
        </SiteSettingsProvider>
      </body>
    </html>
  );
}
