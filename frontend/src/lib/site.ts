export const site = {
  name: "Silas Développe",
  shortName: "Silas",
  tagline: "Consultant numérique, studio digital et académie de formation à Kinshasa.",
  description:
    "Silas Masimango — consultant numérique, fondateur du Studio Silas Développe et de la SDev Academy. Stratégie, produits digitaux et formation pour l'Afrique.",
  url: "https://silasmas.cd",
  email: "ir-masimango@silasmas.com",
  phone: "+243 827 839 232",
  phoneAlt: "+243 993 107 499",
  location: "01, av. des Oliviers, Limete 7e Rue — Kinshasa, RDC",
  socials: {
    linkedin: "https://www.linkedin.com/in/silasmas",
    github: "https://github.com/silasmas",
    youtube: "https://youtube.com/@silasmas",
    x: "https://x.com/silasmas",
  },
} as const;

export const nav = [
  { href: "/", label: "Accueil" },
  { href: "/silas", label: "Silas" },
  { href: "/studio", label: "Studio" },
  { href: "/portfolio", label: "Portfolio" },
  { href: "/academy", label: "Academy" },
  { href: "/contact", label: "Contact" },
] as const;
