import type { Project as ContentProject } from "@/lib/content";
import { stock } from "@/lib/stock";
import { resolveStorageUrl } from "@/lib/api";
import type { Project as ApiProject } from "@/types/api";

const STOCK_COVERS = Object.values(stock.projects);

const VALID_CATEGORIES = [
  "Site Web",
  "Application",
  "Branding",
  "Plateforme",
] as const;

type PortfolioCategory = ContentProject["category"];

/**
 * Génère un slug URL à partir d'un projet API.
 */
export function projectSlug(project: ApiProject): string {
  if (project.slug?.trim()) {
    return project.slug.trim();
  }

  return project.project_name
    .toLowerCase()
    .normalize("NFD")
    .replace(/[\u0300-\u036f]/g, "")
    .replace(/[^a-z0-9]+/g, "-")
    .replace(/^-|-$/g, "");
}

/**
 * Extrait l'année depuis project_date (ex. "2025", "Septembre 2021").
 */
function extractYear(projectDate?: string | null): string {
  if (!projectDate) {
    return "2024";
  }

  const match = projectDate.match(/\d{4}/);

  return match ? match[0] : projectDate;
}

/**
 * Normalise la catégorie portfolio.
 */
function normalizeCategory(project: ApiProject): PortfolioCategory {
  const raw = (project.category ?? "").trim();

  if (VALID_CATEGORIES.includes(raw as PortfolioCategory)) {
    return raw as PortfolioCategory;
  }

  const text = `${raw} ${project.project_description ?? ""}`.toLowerCase();

  if (text.includes("app") || text.includes("mobile")) {
    return "Application";
  }

  if (text.includes("brand") || text.includes("logo")) {
    return "Branding";
  }

  if (text.includes("plateforme") || text.includes("platform")) {
    return "Plateforme";
  }

  return "Site Web";
}

/**
 * Construit les liens externes du projet.
 */
function buildLinks(project: ApiProject): ContentProject["links"] {
  const links: NonNullable<ContentProject["links"]> = [];

  if (project.web_url) {
    links.push({ label: "Voir le site", href: project.web_url });
  }

  if (project.android_url) {
    links.push({ label: "Android", href: project.android_url });
  }

  if (project.ios_url) {
    links.push({ label: "iOS", href: project.ios_url });
  }

  return links.length > 0 ? links : undefined;
}

/**
 * Convertit un projet API Laravel en format contenu portfolio sdev.
 */
export function mapApiProjectToContent(
  project: ApiProject,
  index = 0
): ContentProject {
  const slug = projectSlug(project);
  const cover =
    resolveStorageUrl(project.logo_url) ??
    resolveStorageUrl(project.gallery_urls?.[0]) ??
    STOCK_COVERS[index % STOCK_COVERS.length];
  const excerpt =
    project.project_description ?? "Projet réalisé par Silas Développe.";
  const year = extractYear(project.project_date);
  const category = normalizeCategory(project);
  const tags = project.tags?.length ? project.tags : category ? [category] : [];
  const metrics = project.metrics?.length
    ? project.metrics
    : [
        { label: "Type", value: category },
        { label: "Année", value: year },
        { label: "Statut", value: "Livré" },
      ];

  return {
    slug,
    title: project.project_name,
    client: project.client_name ?? project.project_name,
    category,
    year,
    excerpt,
    cover,
    tags,
    context:
      project.context ??
      excerpt,
    challenge:
      project.challenge ??
      "Concilier exigences métier, contraintes techniques et délais de livraison.",
    outcome:
      project.outcome ??
      "Une solution livrée, maintenue et utilisée par le client au quotidien.",
    metrics,
    links: buildLinks(project),
  };
}

/**
 * Fusionne projets API et contenu statique (fallback si API vide).
 */
export function mergeProjects(
  apiProjects: ApiProject[],
  staticProjects: ContentProject[]
): ContentProject[] {
  if (apiProjects.length === 0) {
    return staticProjects;
  }

  return apiProjects.map(mapApiProjectToContent);
}
