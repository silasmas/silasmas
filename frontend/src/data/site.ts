import type { Project } from "@/types/api";

export const EXPERTISE_ITEMS = [
  "Programmation",
  "Développement Web et Mobile",
  "Design",
  "Community Management",
];

export const SKILLS = [
  { name: "HTML", value: 90 },
  { name: "CSS", value: 90 },
  { name: "JavaScript", value: 75 },
  { name: "PHP", value: 70 },
  { name: "Bootstrap", value: 60 },
  { name: "Laravel", value: 75 },
  { name: "React JS", value: 65 },
  { name: "React Native", value: 90 },
  { name: "Photoshop", value: 85 },
];

export const SERVICES = [
  {
    title: "Création site web",
    description:
      "Nous créons et hébergeons des sites web pour les entreprises et les personnes.",
    icon: "globe",
  },
  {
    title: "Création applis",
    description:
      "Nous créons des applis mobiles Android et iOS pour les entreprises et les personnes.",
    icon: "mobile",
  },
  {
    title: "Marketing",
    description:
      "Nous facilitons la visibilité de vos produits chez vos clients physiquement et en ligne.",
    icon: "marketing",
  },
  {
    title: "Design & Montage",
    description:
      "Nous concevons des affiches, des tracts, des invitations, des logos et autres.",
    icon: "design",
  },
];

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
