import type { SiteAbout, SiteService, SiteSkill } from "@/types/api";

export const FALLBACK_HERO_TAGLINES = [
  "Programmation",
  "Développement Web et Mobile",
  "Design",
  "Community Management",
];

export const FALLBACK_SKILLS: SiteSkill[] = [
  { id: 1, name: "HTML", value: 90 },
  { id: 2, name: "CSS", value: 90 },
  { id: 3, name: "JavaScript", value: 75 },
  { id: 4, name: "PHP", value: 70 },
  { id: 5, name: "Bootstrap", value: 60 },
  { id: 6, name: "Laravel", value: 75 },
  { id: 7, name: "React JS", value: 65 },
  { id: 8, name: "React Native", value: 90 },
  { id: 9, name: "Photoshop", value: 85 },
];

export const FALLBACK_SERVICES: SiteService[] = [
  {
    id: 1,
    title: "Création site web",
    description:
      "Nous créons et hébergeons des sites web pour les entreprises et les personnes.",
    icon: "globe",
  },
  {
    id: 2,
    title: "Création applis",
    description:
      "Nous créons des applis mobiles Android et iOS pour les entreprises et les personnes.",
    icon: "mobile",
  },
  {
    id: 3,
    title: "Marketing",
    description:
      "Nous facilitons la visibilité de vos produits chez vos clients physiquement et en ligne.",
    icon: "marketing",
  },
  {
    id: 4,
    title: "Design & Montage",
    description:
      "Nous concevons des affiches, des tracts, des invitations, des logos et autres.",
    icon: "design",
  },
];

export const FALLBACK_ABOUT: SiteAbout = {
  eyebrow: "À propos",
  title: "Une vision numérique pour l'Afrique",
  body:
    "SDEV est une société offrant des solutions informatiques, des accompagnements et conseils en stratégie marketing digitale et assure la couverture médiatique des évènements de tout genre.",
  secondary_body:
    "Avec SDev Academy, nous formons la prochaine génération de talents du numérique en RDC et sur le continent.",
  image: "/images/logo.png",
};

export const FALLBACK_SITE_SETTINGS = {
  site_title: "Silas Développe",
  site_tagline: "Solutions numériques & SDev Academy",
  logo_url: null,
  favicon_url: null,
  email: "ir-masimango@silasmas.com",
  phone_primary: "(+243) 827 839 232",
  phone_secondary: "(+243) 993 107 499",
  address: "01, av. des Oliviers, Limete 7ème Rue — Kinshasa, RDC",
  footer_description:
    "SDEV offre des solutions informatiques, des accompagnements et conseils en stratégie marketing digitale.",
  usd_to_cdf_rate: 2850,
};
