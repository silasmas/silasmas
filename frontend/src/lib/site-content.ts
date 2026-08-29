import type { SiteContent } from "@/types/api";
import {
  faqs as staticFaqs,
  principles as staticPrinciples,
  services as staticStudioServices,
  testimonials as staticTestimonials,
} from "@/lib/content";
import {
  FALLBACK_ABOUT,
  FALLBACK_HERO_TAGLINES,
  FALLBACK_SERVICES,
  FALLBACK_SKILLS,
  FALLBACK_SITE_SETTINGS,
} from "@/data/site-fallbacks";

export const DEFAULT_HERO = {
  eyebrow: "Édition 2026 — Kinshasa, RDC",
  headline: "Construire des produits numériques",
  headlineAccent: "qui comptent",
  headlineSuffix: "pour l'Afrique.",
  body:
    "Silas Masimango — consultant numérique, fondateur de l'agence Silas Développe et de la SDev Academy. On accompagne, on construit, on transmet.",
  image: null as string | null,
};

export const DEFAULT_CLIENT_LOGOS = [
  "PLA & Associés",
  "Action Damien",
  "Fondation JP Tshienda",
  "GAEL",
  "SkillUp",
  "AGR",
  "Ministère & Partenaires",
];

/**
 * Fusionne le contenu API avec les fallbacks statiques sdev.
 */
export function mergeSiteContent(api: SiteContent | null) {
  const hero = api?.hero
    ? {
        eyebrow: api.hero.eyebrow ?? DEFAULT_HERO.eyebrow,
        headline: api.hero.headline ?? DEFAULT_HERO.headline,
        headlineAccent: api.hero.headline_accent ?? DEFAULT_HERO.headlineAccent,
        headlineSuffix: DEFAULT_HERO.headlineSuffix,
        body: api.hero.body ?? DEFAULT_HERO.body,
        image: api.hero.image ?? null,
      }
    : DEFAULT_HERO;

  return {
    hero,
    about: api?.about ?? FALLBACK_ABOUT,
    skills: api?.skills?.length ? api.skills : FALLBACK_SKILLS,
    services: api?.services?.length ? api.services : FALLBACK_SERVICES,
    testimonials: api?.testimonials?.length
      ? api.testimonials
      : staticTestimonials,
    principles: api?.principles?.length ? api.principles : staticPrinciples,
    faqs: api?.faqs?.length ? api.faqs : staticFaqs,
    clientLogos: api?.client_logos?.length
      ? api.client_logos
      : DEFAULT_CLIENT_LOGOS,
    heroTaglines: api?.hero_taglines?.length
      ? api.hero_taglines
      : FALLBACK_HERO_TAGLINES,
    settings: api?.settings ?? FALLBACK_SITE_SETTINGS,
  };
}

/**
 * Mappe les services CMS vers le format studio sdev (excerpt + bullets).
 */
export function mapServicesForStudio(
  services: ReturnType<typeof mergeSiteContent>["services"]
) {
  if (services.length === 0) {
    return staticStudioServices;
  }

  return services.map((service, index) => ({
    id: String(service.id ?? index),
    title: service.title,
    excerpt: service.excerpt ?? service.description ?? "",
    bullets: service.description
      ? service.description.split(/[.;]\s+/).filter(Boolean).slice(0, 3)
      : [],
  }));
}
