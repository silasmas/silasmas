import { AboutSection } from "@/components/sections/AboutSection";
import { HeroSection } from "@/components/sections/HeroSection";
import { ServicesSection } from "@/components/sections/ServicesSection";
import { SkillsSection } from "@/components/sections/SkillsSection";
import {
  FALLBACK_ABOUT,
  FALLBACK_HERO_TAGLINES,
  FALLBACK_SERVICES,
  FALLBACK_SKILLS,
} from "@/data/site";
import { getSiteContent } from "@/lib/api";

/**
 * Charge et affiche la section hero (API site).
 */
export async function HeroSectionLoader() {
  const site = await getSiteContent();
  const heroTaglines =
    site?.hero_taglines?.length ? site.hero_taglines : FALLBACK_HERO_TAGLINES;

  return <HeroSection taglines={heroTaglines} />;
}

/**
 * Charge et affiche about, compétences et services (API site).
 */
export async function SiteContentSectionsLoader() {
  const site = await getSiteContent();
  const about = site?.about ?? FALLBACK_ABOUT;
  const skills = site?.skills?.length ? site.skills : FALLBACK_SKILLS;
  const services = site?.services?.length ? site.services : FALLBACK_SERVICES;

  return (
    <>
      <AboutSection about={about} />
      <SkillsSection skills={skills} />
      <ServicesSection services={services} />
    </>
  );
}
