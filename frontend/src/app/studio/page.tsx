import type { Metadata } from "next";
import Image from "next/image";
import { ArrowRight } from "lucide-react";
import { Container } from "@/components/site/Container";
import { Eyebrow } from "@/components/site/Eyebrow";
import { Reveal } from "@/components/site/Reveal";
import { CTA } from "@/components/site/CTA";
import { getSiteContent } from "@/lib/api";
import { mapServicesForStudio } from "@/lib/site-content";
import { mergeSiteContent } from "@/lib/site-content";
import { stock } from "@/lib/stock";

import { buildPageMetadata } from "@/lib/seo";

export const metadata: Metadata = buildPageMetadata({
  title: "Studio — Services numériques",
  description:
    "Le studio Silas Développe : conception produit, développement web et mobile, design, marketing et IA appliquée.",
  path: "/studio",
});

const process = [
  {
    n: "01",
    title: "Cadrage",
    body: "Atelier court pour clarifier la cible, le périmètre et les enjeux. On en sort avec un plan partagé.",
  },
  {
    n: "02",
    title: "Design",
    body: "Maquettes, design system, prototypes interactifs. On valide l&rsquo;UX avant la moindre ligne de code.",
  },
  {
    n: "03",
    title: "Build",
    body: "Développement par sprints courts, avec démos hebdomadaires et revue de qualité continue.",
  },
  {
    n: "04",
    title: "Lancement",
    body: "Mise en ligne, mesure, communication. On reste à vos côtés pour les premières semaines de vie.",
  },
];

const stack = [
  { area: "Front-end", items: ["Next.js", "React", "TypeScript", "Tailwind"] },
  { area: "Mobile", items: ["React Native", "Expo", "Swift / Kotlin"] },
  { area: "Back-end", items: ["Laravel", "Node.js", "PostgreSQL"] },
  { area: "Plateforme", items: ["Vercel", "AWS", "Cloudflare"] },
  { area: "Design", items: ["Figma", "Framer", "Design system"] },
  { area: "IA", items: ["OpenAI", "Vector DB", "Agents"] },
];

export default async function StudioPage() {
  const site = mergeSiteContent(await getSiteContent());
  const services = mapServicesForStudio(site.services);

  return (
    <>
      <section className="pt-12 pb-16 md:pt-20 md:pb-24">
        <Container>
          <Eyebrow>Le studio</Eyebrow>
          <h1 className="font-display mt-6 max-w-5xl text-5xl leading-[1.02] tracking-tight md:text-7xl lg:text-[5.5rem]">
            Un atelier numérique pour concevoir, designer et{" "}
            <span className="italic text-accent">livrer</span>.
          </h1>
          <p className="mt-7 max-w-3xl text-lg text-muted leading-relaxed md:text-xl">
            Le studio Silas Développe réunit des designers, des ingénieurs
            et des stratèges autour d&apos;une même obsession : transformer
            une vision en produit numérique réellement utilisé.
          </p>
        </Container>
      </section>

      <section className="border-y border-line bg-bg-elev py-20 md:py-28">
        <Container>
          <div className="flex flex-col items-start justify-between gap-6 md:flex-row md:items-end">
            <div className="max-w-2xl">
              <Eyebrow>Services</Eyebrow>
              <h2 className="font-display mt-4 text-4xl leading-[1.05] tracking-tight md:text-5xl">
                Quatre offres,
                <br />
                <span className="italic">un même standard.</span>
              </h2>
            </div>
          </div>

          <div className="mt-12 grid gap-4 md:grid-cols-2 md:gap-5">
            {services.map((s, i) => (
              <Reveal key={s.id} delay={i * 0.04}>
                <article
                  id={s.id}
                  className="card-lg group flex h-full flex-col p-8 md:p-10"
                >
                  <p className="font-mono text-xs text-muted">
                    0{i + 1} / 04
                  </p>
                  <h3 className="font-display mt-5 text-3xl tracking-tight md:text-4xl">
                    {s.title}
                  </h3>
                  <p className="mt-4 max-w-xl text-base text-muted leading-relaxed md:text-lg">
                    {s.excerpt}
                  </p>
                  <ul className="mt-8 space-y-2 text-sm text-ink-soft">
                    {s.bullets.map((b) => (
                      <li key={b} className="flex items-start gap-2">
                        <span className="mt-1 inline-block size-1.5 shrink-0 rounded-full bg-accent" />
                        {b}
                      </li>
                    ))}
                  </ul>
                </article>
              </Reveal>
            ))}
          </div>
        </Container>
      </section>

      <section className="py-12 md:py-20">
        <Container>
          <div className="grid gap-4 md:grid-cols-12 md:gap-5">
            <div className="relative aspect-[4/3] overflow-hidden rounded-[24px] border border-line md:col-span-7 md:aspect-[16/10]">
              <Image
                src={stock.studio.workshop}
                alt="Atelier — équipe en collaboration"
                fill
                sizes="(min-width: 768px) 58vw, 100vw"
                className="object-cover"
              />
              <div
                aria-hidden
                className="absolute inset-0 bg-gradient-to-t from-ink/50 via-transparent to-transparent"
              />
              <div className="absolute inset-x-6 bottom-6 text-white md:inset-x-8 md:bottom-8">
                <p className="font-mono text-[0.7rem] uppercase tracking-[0.2em] text-white/70">
                  Atelier produit · Kinshasa
                </p>
                <p className="font-display mt-2 text-2xl tracking-tight md:text-3xl">
                  Penser ensemble, livrer vite.
                </p>
              </div>
            </div>
            <div className="grid gap-4 md:col-span-5 md:gap-5">
              <div className="relative aspect-[4/3] overflow-hidden rounded-[24px] border border-line">
                <Image
                  src={stock.studio.code}
                  alt="Développement produit"
                  fill
                  sizes="(min-width: 768px) 40vw, 100vw"
                  className="object-cover"
                />
              </div>
              <div className="relative aspect-[4/3] overflow-hidden rounded-[24px] border border-line">
                <Image
                  src={stock.studio.design}
                  alt="Design produit & branding"
                  fill
                  sizes="(min-width: 768px) 40vw, 100vw"
                  className="object-cover"
                />
              </div>
            </div>
          </div>
        </Container>
      </section>

      <section className="py-24 md:py-32">
        <Container>
          <div className="grid gap-12 md:grid-cols-12">
            <div className="md:col-span-5">
              <Eyebrow>Méthode</Eyebrow>
              <h2 className="font-display mt-4 text-4xl leading-[1.05] tracking-tight md:text-5xl">
                Quatre étapes claires.
                <br />
                Aucune zone grise.
              </h2>
              <p className="mt-6 max-w-md text-base text-muted leading-relaxed md:text-lg">
                Notre process est conçu pour qu&apos;à chaque sprint, le
                client sache exactement où l&apos;on en est et ce qui suit.
              </p>
            </div>
            <ol className="md:col-span-7 divide-y divide-line border-y border-line">
              {process.map((step, i) => (
                <Reveal as="li" key={step.n} delay={i * 0.05}>
                  <div className="grid grid-cols-12 gap-4 py-7 md:py-9">
                    <div className="col-span-2 md:col-span-1">
                      <p className="font-mono text-xs text-muted">
                        {step.n}
                      </p>
                    </div>
                    <div className="col-span-10 md:col-span-11">
                      <h3 className="font-display text-2xl tracking-tight md:text-3xl">
                        {step.title}
                      </h3>
                      <p className="mt-2 max-w-xl text-base text-muted leading-relaxed">
                        {step.body}
                      </p>
                    </div>
                  </div>
                </Reveal>
              ))}
            </ol>
          </div>
        </Container>
      </section>

      <section className="border-y border-line bg-bg-elev py-24 md:py-32">
        <Container>
          <div className="max-w-2xl">
            <Eyebrow>Stack</Eyebrow>
            <h2 className="font-display mt-4 text-4xl leading-[1.05] tracking-tight md:text-5xl">
              Des outils choisis,
              <br />
              <span className="italic">pas accumulés.</span>
            </h2>
          </div>
          <div className="mt-12 grid gap-3 sm:grid-cols-2 md:grid-cols-3">
            {stack.map((s, i) => (
              <Reveal key={s.area} delay={i * 0.04}>
                <div className="card flex h-full flex-col p-6">
                  <p className="eyebrow">{s.area}</p>
                  <ul className="mt-4 flex flex-wrap gap-1.5">
                    {s.items.map((it) => (
                      <li
                        key={it}
                        className="rounded-full border border-line bg-bg px-2.5 py-1 text-xs text-ink-soft"
                      >
                        {it}
                      </li>
                    ))}
                  </ul>
                </div>
              </Reveal>
            ))}
          </div>
          <div className="mt-12 flex">
            <a
              href="/portfolio"
              className="inline-flex items-center gap-2 text-sm font-medium text-ink hover:text-accent"
            >
              Voir nos réalisations
              <ArrowRight className="size-4" />
            </a>
          </div>
        </Container>
      </section>

      <CTA
        title="Donner forme à votre prochain projet."
        subtitle="Un MVP en 6 semaines, un site sur-mesure, une refonte produit. Présentez-nous votre idée."
        cta="Démarrer un projet"
      />
    </>
  );
}
