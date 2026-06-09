import type { Metadata } from "next";
import Link from "next/link";
import Image from "next/image";
import { notFound } from "next/navigation";
import { ArrowLeft, ArrowUpRight } from "lucide-react";
import { Container } from "@/components/site/Container";
import { Eyebrow } from "@/components/site/Eyebrow";
import { CTA } from "@/components/site/CTA";
import { getProjects } from "@/lib/api";
import { projects as staticProjects } from "@/lib/content";
import { mergeProjects } from "@/lib/project-mapper";
import type { Project } from "@/lib/content";

type Params = { slug: string };

/**
 * Charge tous les projets (API + fallback statique).
 */
async function loadProjects(): Promise<Project[]> {
  const apiProjects = await getProjects();
  return mergeProjects(apiProjects, staticProjects);
}

export async function generateStaticParams(): Promise<Params[]> {
  const projects = await loadProjects();
  return projects.map((project) => ({ slug: project.slug }));
}

export async function generateMetadata({
  params,
}: {
  params: Promise<Params>;
}): Promise<Metadata> {
  const { slug } = await params;
  const projects = await loadProjects();
  const project = projects.find((item) => item.slug === slug);

  if (!project) {
    return { title: "Projet introuvable" };
  }

  return {
    title: project.title,
    description: project.excerpt,
  };
}

/**
 * Page détail d'un projet portfolio.
 */
export default async function ProjectPage({
  params,
}: {
  params: Promise<Params>;
}) {
  const { slug } = await params;
  const projects = await loadProjects();
  const project = projects.find((item) => item.slug === slug);

  if (!project) {
    notFound();
  }

  const idx = projects.findIndex((item) => item.slug === slug);
  const next = projects[(idx + 1) % projects.length];

  return (
    <>
      <section className="pt-10 pb-12 md:pt-16 md:pb-16">
        <Container>
          <Link
            href="/portfolio"
            className="inline-flex items-center gap-2 text-sm text-muted hover:text-ink"
          >
            <ArrowLeft className="size-4" />
            Tous les projets
          </Link>

          <div className="mt-8 grid items-end gap-10 md:grid-cols-12">
            <div className="md:col-span-8">
              <Eyebrow>
                {project.category} — {project.year}
              </Eyebrow>
              <h1 className="font-display mt-6 text-5xl leading-[1.02] tracking-tight md:text-7xl">
                {project.title}
              </h1>
              <p className="mt-6 max-w-2xl text-lg text-muted leading-relaxed md:text-xl">
                {project.excerpt}
              </p>
            </div>
            <aside className="md:col-span-4">
              <div className="card-lg p-6 md:p-7">
                <p className="eyebrow">Client</p>
                <p className="mt-3 font-medium text-ink">{project.client}</p>
                <div className="my-5 h-px bg-line" />
                <p className="eyebrow">Stack</p>
                <ul className="mt-3 flex flex-wrap gap-1.5">
                  {project.tags.map((tag) => (
                    <li
                      key={tag}
                      className="rounded-full border border-line bg-bg px-2.5 py-1 text-xs text-ink-soft"
                    >
                      {tag}
                    </li>
                  ))}
                </ul>
                {project.links?.map((link) => (
                  <a
                    key={link.href}
                    href={link.href}
                    target="_blank"
                    rel="noreferrer"
                    className="mt-5 inline-flex items-center gap-1.5 text-sm text-accent hover:underline"
                  >
                    {link.label}
                    <ArrowUpRight className="size-3.5" />
                  </a>
                ))}
              </div>
            </aside>
          </div>
        </Container>
      </section>

      <section>
        <Container>
          <div className="relative aspect-[16/9] w-full overflow-hidden rounded-[24px] border border-line">
            <Image
              src={project.cover}
              alt={project.title}
              fill
              priority
              sizes="(min-width: 1024px) 1100px, 100vw"
              className="object-cover"
            />
            <div
              aria-hidden
              className="absolute inset-0 bg-gradient-to-t from-ink/40 via-transparent to-transparent"
            />
          </div>
        </Container>
      </section>

      <section className="py-20 md:py-28">
        <Container>
          <div className="grid gap-12 md:grid-cols-12">
            <div className="md:col-span-4">
              <Eyebrow>Étude de cas</Eyebrow>
            </div>
            <div className="md:col-span-8 space-y-12">
              <Block title="Contexte" body={project.context} />
              <Block title="Enjeux" body={project.challenge} />
              <Block title="Résultat" body={project.outcome} />
            </div>
          </div>
        </Container>
      </section>

      <section className="border-y border-line bg-bg-elev py-20 md:py-24">
        <Container>
          <div className="grid gap-5 md:grid-cols-3">
            {project.metrics.map((metric) => (
              <div key={metric.label} className="card p-7">
                <p className="eyebrow">{metric.label}</p>
                <p className="font-display mt-8 text-5xl tracking-tight md:text-6xl">
                  {metric.value}
                </p>
              </div>
            ))}
          </div>
        </Container>
      </section>

      <section className="py-20">
        <Container>
          <Link
            href={`/portfolio/${next.slug}`}
            className="group flex items-center justify-between rounded-[24px] border border-line bg-bg-elev p-7 transition-colors hover:border-line-strong md:p-10"
          >
            <div>
              <p className="eyebrow">Projet suivant</p>
              <h3 className="font-display mt-3 text-3xl tracking-tight md:text-5xl">
                {next.title}
              </h3>
            </div>
            <span className="surface-strong inline-flex size-12 items-center justify-center rounded-full transition-transform duration-300 group-hover:-translate-y-0.5 group-hover:translate-x-0.5">
              <ArrowUpRight className="size-5" />
            </span>
          </Link>
        </Container>
      </section>

      <CTA
        title="Un projet similaire ?"
        subtitle="Décrivez-nous votre idée, nous reviendrons vers vous avec une première lecture."
        cta="Démarrer un projet"
      />
    </>
  );
}

function Block({ title, body }: { title: string; body: string }) {
  return (
    <div>
      <h2 className="font-display text-3xl tracking-tight md:text-4xl">
        {title}
      </h2>
      <p className="mt-4 max-w-2xl text-base text-ink-soft leading-relaxed md:text-lg">
        {body}
      </p>
    </div>
  );
}
