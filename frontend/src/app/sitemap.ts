import type { MetadataRoute } from "next";
import { getOpenSessions, getProjects } from "@/lib/api";
import { siteBaseUrl } from "@/lib/seo";

/** Routes statiques indexables. */
const STATIC_PATHS = [
  "",
  "/silas",
  "/agence",
  "/portfolio",
  "/academy",
  "/contact",
];

/**
 * Génère le sitemap XML du site (pages statiques + Academy + portfolio).
 */
export default async function sitemap(): Promise<MetadataRoute.Sitemap> {
  const base = siteBaseUrl();
  const now = new Date();

  const entries: MetadataRoute.Sitemap = STATIC_PATHS.map((path) => ({
    url: `${base}${path || "/"}`,
    lastModified: now,
    changeFrequency: path === "" ? "weekly" : "monthly",
    priority: path === "" ? 1 : 0.7,
  }));

  try {
    const [sessions, projects] = await Promise.all([
      getOpenSessions(),
      getProjects(),
    ]);

    for (const session of sessions) {
      entries.push({
        url: `${base}/academy/${session.slug}`,
        lastModified: now,
        changeFrequency: "weekly",
        priority: 0.9,
      });
    }

    for (const project of projects) {
      if (!project.slug) {
        continue;
      }

      entries.push({
        url: `${base}/portfolio/${project.slug}`,
        lastModified: now,
        changeFrequency: "monthly",
        priority: 0.6,
      });
    }
  } catch {
    // API indisponible au build : sitemap statique uniquement
  }

  return entries;
}
