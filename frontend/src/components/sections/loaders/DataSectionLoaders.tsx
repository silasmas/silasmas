import { AcademySection } from "@/components/sections/AcademySection";
import { PortfolioSection } from "@/components/sections/PortfolioSection";
import { FALLBACK_PROJECTS } from "@/data/site";
import { getOpenSessions, getProjects } from "@/lib/api";

/**
 * Charge et affiche le portfolio (API projets).
 */
export async function PortfolioSectionLoader() {
  const apiProjects = await getProjects();
  const projects = apiProjects.length > 0 ? apiProjects : FALLBACK_PROJECTS;

  return <PortfolioSection projects={projects} />;
}

/**
 * Charge et affiche la section Academy (API sessions).
 */
export async function AcademySectionLoader() {
  const sessions = await getOpenSessions();

  return <AcademySection sessions={sessions} />;
}
