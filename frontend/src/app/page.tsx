import { Hero } from "@/components/site/Hero";
import { RealisationsMarquee } from "@/components/site/RealisationsMarquee";
import { PillarsGrid } from "@/components/site/PillarsGrid";
import { FeaturedWork } from "@/components/site/FeaturedWork";
import { Manifesto } from "@/components/site/Manifesto";
import { AcademyTeaser } from "@/components/site/AcademyTeaser";
import { Testimonials } from "@/components/site/Testimonials";
import { CTA } from "@/components/site/CTA";
import { VideoShowcase } from "@/components/site/VideoShowcase";
import { getOpenSessions, getProjects, getSiteContent } from "@/lib/api";
import { projects as staticProjects } from "@/lib/content";
import { mergeProjects } from "@/lib/project-mapper";
import { mergeSiteContent } from "@/lib/site-content";
import { pickPrimarySession } from "@/lib/sessions";
import { stock } from "@/lib/stock";

/**
 * Page d'accueil — design sdev avec contenu CMS + API.
 */
export default async function HomePage() {
  const [apiProjects, openSessions, siteApi] = await Promise.all([
    getProjects(),
    getOpenSessions(),
    getSiteContent(),
  ]);
  const site = mergeSiteContent(siteApi);
  const projects = mergeProjects(apiProjects, staticProjects);
  const primarySession = pickPrimarySession(openSessions);

  return (
    <>
      <Hero content={site.hero} />
      <RealisationsMarquee projects={projects} />
      <PillarsGrid />
      <FeaturedWork projects={projects} />
      <Manifesto principles={site.principles} />
      <VideoShowcase
        eyebrow="Une journée à l'agence"
        title="On construit pour le réel."
        description="Quelques secondes dans notre quotidien : conception, design, code, livraison."
        src={stock.video.src}
        poster={stock.video.poster}
      />
      <AcademyTeaser session={primarySession} />
      <Testimonials testimonials={site.testimonials} />
      <CTA />
    </>
  );
}
