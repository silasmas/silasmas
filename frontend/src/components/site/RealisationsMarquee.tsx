import Link from "next/link";
import Image from "next/image";
import type { Project } from "@/lib/content";
import { Marquee } from "./Marquee";

/**
 * Bandeau défilant des réalisations — chaque vignette ouvre le détail du projet.
 */
export function RealisationsMarquee({ projects }: { projects: Project[] }) {
  if (projects.length === 0) return null;

  return (
    <section
      aria-label="Nos réalisations"
      className="border-y border-line bg-bg-elev py-6"
    >
      <Marquee>
        {projects.map((project) => (
          <Link
            key={project.slug}
            href={`/portfolio/${project.slug}`}
            className="group flex shrink-0 items-center gap-3 rounded-full border border-line bg-bg py-1.5 pl-1.5 pr-5 transition-colors hover:border-line-strong"
          >
            <span className="relative size-11 shrink-0 overflow-hidden rounded-full">
              <Image
                src={project.cover}
                alt={project.title}
                fill
                sizes="44px"
                className="object-cover transition-transform duration-500 group-hover:scale-110"
              />
            </span>
            <span className="whitespace-nowrap">
              <span className="font-display block text-base tracking-tight text-ink">
                {project.title}
              </span>
              <span className="block text-xs text-muted">
                {project.category} · {project.year}
              </span>
            </span>
          </Link>
        ))}
      </Marquee>
    </section>
  );
}
