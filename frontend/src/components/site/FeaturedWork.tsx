import Link from "next/link";
import Image from "next/image";
import { ArrowUpRight } from "lucide-react";
import type { Project } from "@/lib/content";
import { projects as staticProjects } from "@/lib/content";
import { Container } from "./Container";
import { Eyebrow } from "./Eyebrow";
import { Reveal } from "./Reveal";

/**
 * Grille de projets mis en avant (API ou contenu statique).
 */
export function FeaturedWork({ projects = staticProjects }: { projects?: Project[] }) {
  const featured = projects.slice(0, 4);
  return (
    <section className="py-24 md:py-32">
      <Container>
        <div className="flex flex-col items-start justify-between gap-6 md:flex-row md:items-end">
          <div className="max-w-2xl">
            <Eyebrow>Réalisations sélectionnées</Eyebrow>
            <h2 className="font-display mt-4 text-4xl leading-[1.05] tracking-tight md:text-6xl">
              Quelques projets dont nous sommes <span className="italic">fiers</span>.
            </h2>
          </div>
          <Link
            href="/portfolio"
            className="inline-flex items-center gap-2 text-sm font-medium text-ink hover:text-accent"
          >
            Voir le portfolio complet
            <ArrowUpRight className="size-4" />
          </Link>
        </div>

        <div className="mt-14 grid gap-5 md:grid-cols-12 md:gap-6">
          {featured.map((p, i) => {
            const span =
              i === 0
                ? "md:col-span-7"
                : i === 1
                  ? "md:col-span-5"
                  : i === 2
                    ? "md:col-span-5"
                    : "md:col-span-7";
            return (
              <Reveal key={p.slug} delay={i * 0.05} className={span}>
                <ProjectCard project={p} large={i === 0} />
              </Reveal>
            );
          })}
        </div>
      </Container>
    </section>
  );
}

function ProjectCard({
  project,
  large = false,
}: {
  project: Project;
  large?: boolean;
}) {
  return (
    <Link
      href={`/portfolio/${project.slug}`}
      className="group block h-full overflow-hidden rounded-[24px] border border-line bg-bg-elev transition-colors hover:border-line-strong"
    >
      <div
        className={[
          "relative flex items-end overflow-hidden",
          large ? "aspect-[16/10]" : "aspect-[4/3]",
        ].join(" ")}
      >
        <Image
          src={project.cover}
          alt={project.title}
          fill
          sizes="(min-width: 1024px) 50vw, 100vw"
          className="object-cover transition-transform duration-700 ease-out group-hover:scale-[1.04]"
        />
        <div
          aria-hidden
          className="absolute inset-0 bg-gradient-to-t from-ink/55 via-ink/10 to-transparent"
        />
        <div className="relative z-10 p-6 md:p-8">
          <span className="inline-flex items-center rounded-full border border-white/30 bg-white/15 px-3 py-1 text-xs text-white backdrop-blur-md">
            {project.category} · {project.year}
          </span>
        </div>
        <div className="absolute right-5 top-5 z-10">
          <span className="surface-strong inline-flex size-10 items-center justify-center rounded-full transition-transform duration-300 group-hover:-translate-y-0.5 group-hover:translate-x-0.5">
            <ArrowUpRight className="size-4" />
          </span>
        </div>
      </div>
      <div className="border-t border-line p-6 md:p-7">
        <h3 className="font-display text-2xl tracking-tight md:text-3xl">
          {project.title}
        </h3>
        <p className="mt-2 text-sm text-muted md:text-base">
          {project.excerpt}
        </p>
        <div className="mt-5 flex flex-wrap gap-1.5">
          {project.tags.map((t) => (
            <span
              key={t}
              className="inline-flex items-center rounded-full border border-line bg-bg px-2.5 py-1 text-[0.72rem] text-muted"
            >
              {t}
            </span>
          ))}
        </div>
      </div>
    </Link>
  );
}
