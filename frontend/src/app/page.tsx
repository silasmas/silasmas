import { AboutSection } from "@/components/sections/AboutSection";
import { AcademySection } from "@/components/sections/AcademySection";
import { ContactSection } from "@/components/sections/ContactSection";
import { HeroSection } from "@/components/sections/HeroSection";
import { PortfolioSection } from "@/components/sections/PortfolioSection";
import { ServicesSection } from "@/components/sections/ServicesSection";
import { SkillsSection } from "@/components/sections/SkillsSection";
import { FALLBACK_PROJECTS } from "@/data/site";
import { getFeaturedSessions, getProjects } from "@/lib/api";

/**
 * Page d'accueil SDEV — même structure que l'ancien site, design ll-academie adapté.
 */
export default async function HomePage() {
  const [apiProjects, sessions] = await Promise.all([
    getProjects(),
    getFeaturedSessions(),
  ]);

  const projects = apiProjects.length > 0 ? apiProjects : FALLBACK_PROJECTS;

  return (
    <>
      <HeroSection />
      <AboutSection />
      <SkillsSection />
      <ServicesSection />
      <PortfolioSection projects={projects} />
      <AcademySection sessions={sessions} />
      <ContactSection />
    </>
  );
}
