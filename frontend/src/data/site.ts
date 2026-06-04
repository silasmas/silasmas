import type { Project } from "@/types/api";
export {
  FALLBACK_ABOUT,
  FALLBACK_HERO_TAGLINES,
  FALLBACK_SERVICES,
  FALLBACK_SKILLS,
} from "@/data/site-fallbacks";

/**
 * Portfolio statique de repli si l'API est vide.
 */
export const FALLBACK_PROJECTS: Project[] = [
  {
    id: 1,
    project_name: "PLA",
    project_description: "Site web",
    logo_url: "/images/portfolio/p1-1.png",
  },
  {
    id: 2,
    project_name: "ACR",
    project_description: "Site web et App Mobile",
    logo_url: "/images/portfolio/p2-2.png",
  },
  {
    id: 3,
    project_name: "JP tshienda",
    project_description: "Site web et App Mobile",
    logo_url: "/images/portfolio/p3-1.png",
  },
  {
    id: 4,
    project_name: "Skyitup",
    project_description: "Site web",
    logo_url: "/images/portfolio/p4-1.png",
  },
  {
    id: 5,
    project_name: "Action Damien",
    project_description: "Site Web",
    logo_url: "/images/portfolio/p5-1.png",
  },
  {
    id: 6,
    project_name: "Groupe Adorons l'éternel",
    project_description: "Site Web",
    logo_url: "/images/portfolio/p6-1.png",
  },
];

export const CONTACT_INFO = {
  address: "01, av. des Oliviers, Limete 7ème Rue",
  email: "ir-masimango@silasmas.com",
  phones: ["(+243) 827 839 232", "(+243) 993 107 499"],
};

export const NAV_LINKS = [
  { href: "#hero", label: "Accueil" },
  { href: "#about", label: "À propos" },
  { href: "#skills", label: "Compétences" },
  { href: "#services", label: "Services" },
  { href: "#portfolio", label: "Portfolio" },
  { href: "#academy", label: "Academy" },
  { href: "#contact", label: "Contact" },
];
