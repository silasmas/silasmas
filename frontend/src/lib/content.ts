export type Service = {
  id: string;
  title: string;
  excerpt: string;
  bullets: string[];
};

import { stock } from "./stock";

export const services: Service[] = [
  {
    id: "produit",
    title: "Produits web & mobile",
    excerpt:
      "Conception et développement d'applications sur-mesure — du MVP à la mise à l'échelle.",
    bullets: [
      "Architecture Next.js, Laravel, React Native",
      "Design system et composants réutilisables",
      "Intégrations API, paiements, notifications",
    ],
  },
  {
    id: "design",
    title: "Identité & design produit",
    excerpt:
      "Une direction artistique cohérente entre la marque, l'interface et le contenu.",
    bullets: [
      "Identité visuelle, logotype, charte",
      "UI/UX premium, prototypes Figma",
      "Design system documenté",
    ],
  },
  {
    id: "marketing",
    title: "Marketing & contenu",
    excerpt:
      "Une présence digitale qui convertit, sans bruit ni recettes datées.",
    bullets: [
      "Stratégie éditoriale et SEO",
      "Lancements produits, landing pages",
      "Campagnes Meta, Google, LinkedIn",
    ],
  },
  {
    id: "ia",
    title: "IA appliquée",
    excerpt:
      "Intégration d'IA générative et d'agents dans vos flux métier — utile, mesurable, sécurisée.",
    bullets: [
      "Agents et copilots internes",
      "RAG, automatisation de contenu",
      "Cadrage, POC, mise en production",
    ],
  },
];

export type Project = {
  slug: string;
  title: string;
  client: string;
  category: "Site Web" | "Application" | "Branding" | "Plateforme";
  year: string;
  excerpt: string;
  cover: string;
  tags: string[];
  context: string;
  challenge: string;
  outcome: string;
  metrics: { label: string; value: string }[];
  links?: { label: string; href: string }[];
};

export const projects: Project[] = [
  {
    slug: "pla-cabinet",
    title: "PLA — Pathy Liongo & Associés",
    client: "Cabinet juridique & finance",
    category: "Site Web",
    year: "2025",
    excerpt:
      "Site bilingue dynamique pour un cabinet panafricain de conseil juridique et financier.",
    cover: stock.projects["pla-cabinet"],
    tags: ["Next.js", "i18n", "Sanity CMS"],
    context:
      "Le cabinet PLA accompagne investisseurs et institutions à travers l'Afrique centrale. Il avait besoin d'un site capable de refléter son sérieux et sa portée internationale.",
    challenge:
      "Concilier la rigueur d'une marque juridique avec une expérience web contemporaine, multilingue et facilement administrable par les équipes.",
    outcome:
      "Une plateforme bilingue (FR/EN), un CMS headless et une architecture modulaire qui sert aujourd'hui de socle à toutes les communications du cabinet.",
    metrics: [
      { label: "Pages multilingues", value: "40+" },
      { label: "Délai de mise à jour", value: "−80%" },
      { label: "Score Lighthouse", value: "98" },
    ],
  },
  {
    slug: "agr-rdc",
    title: "AGR — Application mobile",
    client: "Partie publique — RDC",
    category: "Application",
    year: "2024",
    excerpt:
      "Application mobile multilangue pour l'enregistrement des membres et le suivi terrain.",
    cover: stock.projects["agr-rdc"],
    tags: ["React Native", "Laravel", "Realtime"],
    context:
      "Une institution publique avait besoin d'un outil mobile fiable pour enregistrer et géolocaliser des dizaines de milliers d'adhérents sur le territoire national.",
    challenge:
      "Travailler en zones à connectivité limitée, avec une UX simple pour des opérateurs non-techniques et une supervision en temps réel.",
    outcome:
      "Une app multilangue, mode hors-ligne, synchronisation différée et dashboard administrateur qui a réduit les délais de traitement de plusieurs semaines.",
    metrics: [
      { label: "Adhérents enregistrés", value: "60k+" },
      { label: "Provinces couvertes", value: "12" },
      { label: "Mode hors-ligne", value: "Oui" },
    ],
  },
  {
    slug: "jp-tshienda",
    title: "Fondation Jean-Pierre Tshienda",
    client: "Fondation",
    category: "Plateforme",
    year: "2024",
    excerpt:
      "Site et application mobile pour rassembler la communauté autour d'une figure publique.",
    cover: stock.projects["jp-tshienda"],
    tags: ["Laravel", "React Native", "Firebase"],
    context:
      "Donner à la fondation une vitrine moderne et un canal de mobilisation au-delà des réseaux sociaux.",
    challenge:
      "Gérer un large volume de contenus — articles, événements, médias — et offrir une expérience cohérente sur web et mobile.",
    outcome:
      "Un écosystème éditorial unifié, avec push notifications, agenda et bibliothèque média.",
    metrics: [
      { label: "Téléchargements", value: "8k+" },
      { label: "Articles publiés", value: "200+" },
      { label: "Push envoyés", value: "Hebdo" },
    ],
  },
  {
    slug: "skillup",
    title: "SkillUp — Plateforme RH",
    client: "Entreprise de services",
    category: "Plateforme",
    year: "2025",
    excerpt:
      "Plateforme de gestion des talents et de suivi des compétences pour une PME en croissance.",
    cover: stock.projects.skillup,
    tags: ["Next.js", "PostgreSQL", "Auth"],
    context:
      "Centraliser la gestion des collaborateurs, des évaluations et des plans de carrière dans un seul outil.",
    challenge:
      "Modéliser des processus RH complexes sans alourdir l'interface ; permettre une adoption rapide par des managers non-techniques.",
    outcome:
      "Un produit interne utilisé quotidiennement, qui structure la gestion des talents et alimente la stratégie de l'entreprise.",
    metrics: [
      { label: "Utilisateurs actifs", value: "120" },
      { label: "Processus digitalisés", value: "8" },
      { label: "Adoption interne", value: "92%" },
    ],
  },
  {
    slug: "action-damien",
    title: "Action Damien — RDC",
    client: "ONG internationale",
    category: "Site Web",
    year: "2024",
    excerpt:
      "Site institutionnel multilangue pour un organisme de santé publique présent dans plusieurs pays.",
    cover: stock.projects["action-damien"],
    tags: ["Laravel", "i18n", "CMS"],
    context:
      "L'ONG voulait moderniser sa présence en ligne et donner plus de visibilité à ses programmes terrain.",
    challenge:
      "Fédérer des contenus en plusieurs langues, mettre en avant l'impact, tout en restant simple à administrer.",
    outcome:
      "Un site éditorial clair, multilingue, avec un back-office adapté aux équipes communication.",
    metrics: [
      { label: "Langues", value: "3" },
      { label: "Pays couverts", value: "5" },
      { label: "Temps de chargement", value: "< 1.2s" },
    ],
  },
  {
    slug: "adorons",
    title: "Groupe Adorons l'Éternel",
    client: "Communauté musicale",
    category: "Application",
    year: "2025",
    excerpt:
      "Site, application et boutique pour un collectif musical de référence en Afrique francophone.",
    cover: stock.projects.adorons,
    tags: ["Next.js", "PHP", "Streaming"],
    context:
      "Donner au collectif un canal direct vers sa communauté, indépendamment des plateformes tierces.",
    challenge:
      "Combiner contenu éditorial, médiathèque, boutique et communauté dans une expérience cohérente.",
    outcome:
      "Une plateforme unifiée qui consolide l'identité du groupe et ses revenus directs.",
    metrics: [
      { label: "Communauté", value: "50k+" },
      { label: "Pistes en ligne", value: "200+" },
      { label: "Conversion shop", value: "+38%" },
    ],
  },
];

export const stats = [
  { value: "8+", label: "Années d'expérience" },
  { value: "40+", label: "Projets livrés" },
  { value: "12", label: "Pays touchés" },
  { value: "300+", label: "Apprenants formés" },
];

export const testimonials = [
  {
    quote:
      "Silas a transformé notre vision en un produit digital crédible. C'est rare de trouver autant de clarté stratégique chez un développeur.",
    author: "Pathy Liongo",
    role: "Associé, PLA & Associés",
  },
  {
    quote:
      "Travailler avec l'agence, c'est obtenir un partenaire qui pense produit avant de penser code. Notre application a été livrée à temps, propre et évolutive.",
    author: "M. Kabongo",
    role: "Directeur des opérations",
  },
  {
    quote:
      "La formation SDev Academy est la meilleure que j'ai suivie à Kinshasa. Concrète, exigeante, et tournée sur de vrais projets.",
    author: "Grâce N.",
    role: "Développeuse, promotion 2025",
  },
];

export type Module = {
  number: string;
  title: string;
  duration: string;
  outline: string[];
};

export const academyTracks: { id: string; title: string; description: string; modules: Module[] }[] = [
  {
    id: "ia",
    title: "Programmation assistée par l'IA",
    description:
      "Apprendre à concevoir, coder et livrer des produits en partenariat avec l'intelligence artificielle.",
    modules: [
      {
        number: "01",
        title: "Fondations du dev moderne",
        duration: "2 semaines",
        outline: [
          "Git, environnement, ligne de commande",
          "TypeScript et bases de Next.js",
          "Pensée produit & cadrage",
        ],
      },
      {
        number: "02",
        title: "Travailler avec l'IA",
        duration: "3 semaines",
        outline: [
          "Prompting structuré et patterns",
          "Pair-programming avec Copilot, Cursor",
          "Génération de code, tests et docs",
        ],
      },
      {
        number: "03",
        title: "Construire un produit complet",
        duration: "4 semaines",
        outline: [
          "Architecture Next.js full-stack",
          "Auth, base de données, paiements",
          "Déploiement Vercel / VPS",
        ],
      },
      {
        number: "04",
        title: "Projet final & portfolio",
        duration: "3 semaines",
        outline: [
          "Lancement d'un projet réel",
          "Storytelling et démo publique",
          "Préparation à la vie pro",
        ],
      },
    ],
  },
];

export const principles = [
  {
    title: "Penser produit",
    body: "Avant chaque ligne de code, on cadre l'usage, le marché et la valeur. Pas de prouesse technique sans question business.",
  },
  {
    title: "Designer la simplicité",
    body: "Un bon produit se reconnaît à ce qu'il enlève. On défend la clarté contre la complexité.",
  },
  {
    title: "Livrer, vraiment",
    body: "Mise en ligne, mesure, itération. Un projet n'existe pas tant qu'il n'a pas rencontré ses utilisateurs.",
  },
  {
    title: "Former la prochaine génération",
    body: "Chaque mission, chaque cours est aussi une occasion de transmettre. C'est notre manière de faire grandir l'écosystème.",
  },
];

export const faqs = [
  {
    q: "Travaillez-vous uniquement en RDC ?",
    a: "Non. Nous accompagnons des clients en RDC, en Afrique centrale et en Europe — la majorité de nos collaborations se font à distance.",
  },
  {
    q: "Quel est le budget minimum d'un projet ?",
    a: "Une mission produit complète démarre généralement autour de 5 000 USD. Nous proposons aussi des formats plus courts pour le cadrage, l'audit ou un MVP.",
  },
  {
    q: "Quels sont les délais habituels ?",
    a: "Un site sur-mesure : 4 à 8 semaines. Un produit applicatif : 2 à 4 mois. Un cadrage stratégique : 1 à 3 semaines.",
  },
  {
    q: "Comment se déroulent les cohortes Academy ?",
    a: "Chaque cohorte dure 12 semaines, en hybride. Les sessions live, le mentorat et le projet final sont au cœur de l'expérience.",
  },
];
