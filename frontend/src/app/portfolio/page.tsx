import type { Metadata } from "next";
import { Container } from "@/components/site/Container";
import { Eyebrow } from "@/components/site/Eyebrow";
import { PortfolioGrid } from "@/components/site/PortfolioGrid";
import { CTA } from "@/components/site/CTA";
import { getProjects } from "@/lib/api";
import { projects as staticProjects } from "@/lib/content";
import { mergeProjects } from "@/lib/project-mapper";

export const metadata: Metadata = {
  title: "Portfolio",
  description:
    "Sélection de projets web, mobile et plateformes conçus par Silas Développe.",
};

/**
 * Page portfolio — projets depuis l'API Laravel avec repli statique.
 */
export default async function PortfolioPage() {
  const apiProjects = await getProjects();
  const projects = mergeProjects(apiProjects, staticProjects);

  return (
    <>
      <section className="pt-12 pb-12 md:pt-20 md:pb-16">
        <Container>
          <div className="grid items-end gap-8 md:grid-cols-12">
            <div className="md:col-span-8">
              <Eyebrow>Réalisations</Eyebrow>
              <h1 className="font-display mt-6 text-5xl leading-[1.02] tracking-tight md:text-7xl">
                Une décennie de produits livrés,
                <br />
                <span className="italic text-accent">en silence ou à grande échelle.</span>
              </h1>
            </div>
            <p className="md:col-span-4 text-base text-muted md:text-lg">
              Chaque projet est l&apos;occasion d&apos;explorer un secteur,
              une équipe et un terrain. Voici une sélection des plus
              significatifs.
            </p>
          </div>
        </Container>
      </section>

      <section className="pb-24 md:pb-32">
        <Container>
          <PortfolioGrid projects={projects} />
        </Container>
      </section>

      <CTA
        title="Le prochain projet est-il le vôtre ?"
        subtitle="On a hâte de découvrir votre idée. Quelques lignes suffisent pour démarrer la conversation."
        cta="Nous contacter"
      />
    </>
  );
}
