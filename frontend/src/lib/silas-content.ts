import type { SilasJourneyStep, SilasOffer, SilasPageContent } from "@/types/api";
import { stock } from "@/lib/stock";

export type ResolvedSilasPage = {
  hero: {
    eyebrow: string;
    title: string;
    accent: string;
    body: string;
    image: string | null;
  };
  availability: { title: string; body: string };
  journey_intro: { title: string; body: string };
  journey: SilasJourneyStep[];
  banner: { badge: string; title: string; image: string | null };
  offers: SilasOffer[];
  cta: { title: string; subtitle: string; cta: string };
};

export const DEFAULT_SILAS_PAGE: ResolvedSilasPage = {
  hero: {
    eyebrow: "Le consultant",
    title: "Silas Masimango.",
    accent: "Entrepreneur numérique.",
    body:
      "Je conseille des dirigeants, je construis des produits avec mon studio et je forme "
      + "la prochaine génération de développeurs avec la SDev Academy. Ma conviction : "
      + "l'Afrique a besoin de ses propres bâtisseurs numériques.",
    image: stock.silas.portrait,
  },
  availability: {
    title: "Q3 — 2026",
    body: "J'accepte 4 missions de conseil par trimestre.",
  },
  journey_intro: {
    title: "D'une chambre de Kinshasa à un écosystème complet.",
    body:
      "Quelques étapes qui résument bien ce que nous construisons, année après année.",
  },
  journey: [
    {
      id: 1,
      year: "2017",
      title: "Premiers projets web",
      body:
        "Premières missions de développement web et mobile en RDC, principalement avec PHP, Laravel et React.",
    },
    {
      id: 2,
      year: "2020",
      title: "Création du studio",
      body:
        "Naissance de Silas Développe : un atelier numérique pluridisciplinaire au service des entreprises africaines.",
    },
    {
      id: 3,
      year: "2023",
      title: "Premières formations",
      body:
        "Lancement de programmes courts pour former une nouvelle génération de développeurs et de designers.",
    },
    {
      id: 4,
      year: "2025",
      title: "SDev Academy",
      body:
        "Ouverture officielle de l'académie : un programme intensif de 12 semaines centré sur la programmation assistée par l'IA.",
    },
    {
      id: 5,
      year: "2026",
      title: "Studio + Academy + Conseil",
      body:
        "Trois activités, une seule marque : conseil stratégique, studio produit et école — tout au même endroit.",
    },
  ],
  banner: {
    badge: "Conférence — Dakar, 2025",
    title: "Penser produit, à l'échelle du continent.",
    image: stock.silas.speaking,
  },
  offers: [
    {
      id: 1,
      icon: "compass",
      title: "Conseil stratégique",
      body:
        "Sessions 1-1 avec des dirigeants pour cadrer un produit, un lancement ou une transformation digitale.",
    },
    {
      id: 2,
      icon: "lightbulb",
      title: "Audit produit & numérique",
      body:
        "Diagnostic court et opérationnel — UX, code, marque, organisation. Un plan de marche actionnable.",
    },
    {
      id: 3,
      icon: "mic",
      title: "Conférences & masterclasses",
      body:
        "Interventions sur l'IA, la création produit et la nouvelle économie numérique africaine.",
    },
  ],
  cta: {
    title: "Vous avez une décision à prendre ?",
    subtitle:
      "Une session de 60 minutes peut souvent débloquer des semaines d'hésitation. Parlons-en.",
    cta: "Réserver un appel",
  },
};

/**
 * Fusionne le contenu API Silas avec les valeurs par défaut.
 */
export function mergeSilasPage(
  api: SilasPageContent | null | undefined
): ResolvedSilasPage {
  if (!api) {
    return DEFAULT_SILAS_PAGE;
  }

  return {
    hero: {
      eyebrow: api.hero?.eyebrow ?? DEFAULT_SILAS_PAGE.hero.eyebrow,
      title: api.hero?.title ?? DEFAULT_SILAS_PAGE.hero.title,
      accent: api.hero?.accent ?? DEFAULT_SILAS_PAGE.hero.accent,
      body: api.hero?.body ?? DEFAULT_SILAS_PAGE.hero.body,
      image: api.hero?.image ?? DEFAULT_SILAS_PAGE.hero.image,
    },
    availability: {
      title: api.availability?.title ?? DEFAULT_SILAS_PAGE.availability.title,
      body: api.availability?.body ?? DEFAULT_SILAS_PAGE.availability.body,
    },
    journey_intro: {
      title: api.journey_intro?.title ?? DEFAULT_SILAS_PAGE.journey_intro.title,
      body: api.journey_intro?.body ?? DEFAULT_SILAS_PAGE.journey_intro.body,
    },
    journey: api.journey?.length ? api.journey : DEFAULT_SILAS_PAGE.journey,
    banner: {
      badge: api.banner?.badge ?? DEFAULT_SILAS_PAGE.banner.badge,
      title: api.banner?.title ?? DEFAULT_SILAS_PAGE.banner.title,
      image: api.banner?.image ?? DEFAULT_SILAS_PAGE.banner.image,
    },
    offers: api.offers?.length ? api.offers : DEFAULT_SILAS_PAGE.offers,
    cta: {
      title: api.cta?.title ?? DEFAULT_SILAS_PAGE.cta.title,
      subtitle: api.cta?.subtitle ?? DEFAULT_SILAS_PAGE.cta.subtitle,
      cta: api.cta?.cta ?? DEFAULT_SILAS_PAGE.cta.cta,
    },
  };
}
