import type { Metadata } from "next";
import Link from "next/link";
import Image from "next/image";
import { ArrowUpRight } from "lucide-react";
import { Container } from "@/components/site/Container";
import { Eyebrow } from "@/components/site/Eyebrow";
import { Reveal } from "@/components/site/Reveal";
import { CTA } from "@/components/site/CTA";
import { SilasOffers } from "@/components/site/SilasOffers";
import { getSiteContent } from "@/lib/api";
import { mergeSilasPage } from "@/lib/silas-content";

export const metadata: Metadata = {
  title: "Silas — Le consultant",
  description:
    "Silas Masimango : entrepreneur numérique, consultant produit et fondateur de la SDev Academy. Stratégie, accompagnement et conférences.",
};

/**
 * Page Silas — contenu éditorial depuis le CMS Filament.
 */
export default async function SilasPage() {
  const site = await getSiteContent();
  const silas = mergeSilasPage(site?.silas_page);

  return (
    <>
      <section className="pt-12 pb-16 md:pt-20 md:pb-24">
        <Container>
          <div className="grid items-end gap-12 md:grid-cols-12">
            <div className="md:col-span-8">
              <Eyebrow>{silas.hero.eyebrow}</Eyebrow>
              <h1 className="font-display mt-6 text-5xl leading-[1.02] tracking-tight md:text-7xl lg:text-[5.5rem]">
                {silas.hero.title}
                <br />
                <span className="italic text-accent">{silas.hero.accent}</span>
              </h1>
              <p className="mt-7 max-w-2xl text-lg text-muted leading-relaxed md:text-xl">
                {silas.hero.body}
              </p>
            </div>

            <div className="md:col-span-4">
              {silas.hero.image && (
                <div className="relative aspect-[4/5] w-full overflow-hidden rounded-[24px] border border-line">
                  <Image
                    src={silas.hero.image}
                    alt="Silas Masimango"
                    fill
                    priority
                    sizes="(min-width: 768px) 33vw, 100vw"
                    className="object-cover"
                  />
                  <div
                    aria-hidden
                    className="absolute inset-0 bg-gradient-to-t from-ink/55 via-transparent to-transparent"
                  />
                </div>
              )}
              <div className="card-lg mt-4 p-6">
                <p className="eyebrow">Disponibilité</p>
                <p className="font-display mt-3 text-2xl tracking-tight">
                  {silas.availability.title}
                </p>
                <p className="mt-2 text-sm text-muted">{silas.availability.body}</p>
                <Link
                  href="/contact"
                  className="mt-5 inline-flex items-center gap-2 text-sm font-medium text-ink hover:text-accent"
                >
                  Réserver un appel
                  <ArrowUpRight className="size-4" />
                </Link>
              </div>
            </div>
          </div>
        </Container>
      </section>

      <section className="py-20 md:py-28">
        <Container>
          <div className="grid gap-12 md:grid-cols-12">
            <div className="md:col-span-5">
              <Eyebrow>Trajectoire</Eyebrow>
              <h2 className="font-display mt-4 text-4xl leading-[1.05] tracking-tight md:text-5xl">
                {silas.journey_intro.title}
              </h2>
              <p className="mt-6 max-w-md text-base text-muted leading-relaxed md:text-lg">
                {silas.journey_intro.body}
              </p>
            </div>
            <ol className="md:col-span-7 divide-y divide-line border-y border-line">
              {silas.journey.map((step, index) => (
                <Reveal as="li" key={step.id ?? step.year} delay={index * 0.04}>
                  <div className="grid grid-cols-12 gap-4 py-6 md:py-8">
                    <div className="col-span-3 md:col-span-2">
                      <p className="font-mono text-sm text-muted">{step.year}</p>
                    </div>
                    <div className="col-span-9 md:col-span-10">
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

      {silas.banner.image && (
        <section className="py-12 md:py-20">
          <Container>
            <div className="relative aspect-[21/9] w-full overflow-hidden rounded-[28px] border border-line">
              <Image
                src={silas.banner.image}
                alt="Silas Masimango en conférence"
                fill
                sizes="100vw"
                className="object-cover"
              />
              <div
                aria-hidden
                className="absolute inset-0 bg-gradient-to-t from-ink/60 via-ink/10 to-transparent"
              />
              <div className="absolute inset-x-6 bottom-6 md:inset-x-10 md:bottom-10">
                <span className="inline-flex items-center rounded-full border border-white/30 bg-white/15 px-3 py-1 text-xs text-white backdrop-blur-md">
                  {silas.banner.badge}
                </span>
                <h3 className="font-display mt-4 max-w-xl text-3xl tracking-tight text-white md:text-5xl">
                  {silas.banner.title}
                </h3>
              </div>
            </div>
          </Container>
        </section>
      )}

      <section className="border-y border-line bg-bg-elev py-24 md:py-32">
        <Container>
          <div className="max-w-2xl">
            <Eyebrow>Travailler avec moi</Eyebrow>
            <h2 className="font-display mt-4 text-4xl leading-[1.05] tracking-tight md:text-5xl">
              Trois manières de collaborer.
            </h2>
          </div>
          <SilasOffers offers={silas.offers} />
        </Container>
      </section>

      <CTA
        title={silas.cta.title}
        subtitle={silas.cta.subtitle}
        cta={silas.cta.cta}
        href="/contact"
      />
    </>
  );
}
