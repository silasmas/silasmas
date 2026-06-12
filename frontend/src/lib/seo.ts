import type { Metadata } from "next";
import { site } from "@/lib/site";

/** URL canonique du site public. */
export function siteBaseUrl(): string {
  return (process.env.NEXT_PUBLIC_SITE_URL ?? "https://silasmas.com").replace(/\/$/, "");
}

/** Mots-clés SEO principaux du studio. */
export const SITE_KEYWORDS = [
  "Silas Développe",
  "SDev Academy",
  "développement web Kinshasa",
  "formation numérique RDC",
  "studio digital Afrique",
  "consultant numérique",
  "Silas Masimango",
];

interface PageSeoOptions {
  title: string;
  description: string;
  path?: string;
  image?: string | null;
  noIndex?: boolean;
}

/**
 * Construit les métadonnées SEO complètes pour une page.
 *
 * @param options Titre, description et chemin relatif
 * @returns Objet Metadata Next.js
 */
export function buildPageMetadata(options: PageSeoOptions): Metadata {
  const base = siteBaseUrl();
  const path = options.path ?? "/";
  const url = `${base}${path.startsWith("/") ? path : `/${path}`}`;
  const image = options.image ?? `${base}/images/logo.png`;

  return {
    title: options.title,
    description: options.description,
    keywords: SITE_KEYWORDS,
    alternates: {
      canonical: url,
    },
    robots: options.noIndex
      ? { index: false, follow: false }
      : { index: true, follow: true },
    openGraph: {
      title: options.title,
      description: options.description,
      url,
      siteName: site.name,
      locale: "fr_FR",
      type: "website",
      images: [{ url: image, alt: options.title }],
    },
    twitter: {
      card: "summary_large_image",
      title: options.title,
      description: options.description,
      images: [image],
    },
  };
}

/**
 * Schéma JSON-LD Organization + WebSite pour le layout racine.
 *
 * @param siteTitle Nom du site depuis l'API
 * @param description Description du site
 * @returns Objet JSON-LD sérialisable
 */
export function buildOrganizationJsonLd(siteTitle: string, description: string) {
  const base = siteBaseUrl();

  return {
    "@context": "https://schema.org",
    "@graph": [
      {
        "@type": "Organization",
        name: siteTitle,
        url: base,
        logo: `${base}/images/logo.png`,
        email: site.email,
        telephone: site.phone,
        address: {
          "@type": "PostalAddress",
          streetAddress: site.location,
          addressCountry: "CD",
        },
        sameAs: Object.values(site.socials),
      },
      {
        "@type": "WebSite",
        name: siteTitle,
        url: base,
        description,
        inLanguage: "fr-FR",
      },
    ],
  };
}

/**
 * Schéma JSON-LD Course pour une session Academy.
 */
export function buildCourseJsonLd(session: {
  title: string;
  description?: string | null;
  slug: string;
  start_date: string;
  end_date: string;
  formatted_price?: string | null;
  is_free?: boolean;
}) {
  const base = siteBaseUrl();

  return {
    "@context": "https://schema.org",
    "@type": "Course",
    name: session.title,
    description: session.description ?? session.title,
    url: `${base}/academy/${session.slug}`,
    provider: {
      "@type": "Organization",
      name: site.name,
      url: base,
    },
    hasCourseInstance: {
      "@type": "CourseInstance",
      courseMode: "online",
      startDate: session.start_date,
      endDate: session.end_date,
    },
    offers: session.is_free
      ? { "@type": "Offer", price: "0", priceCurrency: "USD" }
      : undefined,
  };
}
