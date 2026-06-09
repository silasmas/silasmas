import type { Metadata } from "next";
import { redirect } from "next/navigation";
import Link from "next/link";
import Image from "next/image";
import { ArrowRight, CheckCircle2 } from "lucide-react";
import { Container } from "@/components/site/Container";
import { Eyebrow } from "@/components/site/Eyebrow";
import { Reveal } from "@/components/site/Reveal";
import { FAQ } from "@/components/site/FAQ";
import { CTA } from "@/components/site/CTA";
import { VideoShowcase } from "@/components/site/VideoShowcase";
import { academyTracks, faqs as staticFaqs } from "@/lib/content";
import { getOpenSessions, getSiteContent } from "@/lib/api";
import { mergeSiteContent } from "@/lib/site-content";
import { isAcademyLaunchMode } from "@/lib/launch";
import { pickPrimarySession } from "@/lib/sessions";
import { stock } from "@/lib/stock";

export const metadata: Metadata = {
  title: "SDev Academy",
  description:
    "L'académie SDev — 12 semaines pour devenir développeur produit à l'ère de l'IA. Mentorat, projets réels et certificat.",
};

const audience = [
  "Étudiants en fin de cursus prêts à passer au monde réel.",
  "Jeunes professionnels en reconversion vers la tech produit.",
  "Designers et chefs de projet qui veulent coder avec l'IA.",
];

const outcomes = [
  "Construire et déployer un produit complet en 12 semaines.",
  "Maîtriser le développement assisté par l'IA en environnement pro.",
  "Sortir avec un portfolio crédible et un réseau d'alumni.",
];

export default async function AcademyPage() {
  const track = academyTracks[0];
  const openSessions = await getOpenSessions();
  const site = mergeSiteContent(await getSiteContent());
  const faqs = site.faqs.length ? site.faqs : staticFaqs;
  const primarySession = pickPrimarySession(openSessions);

  if (isAcademyLaunchMode() && primarySession?.slug) {
    redirect(`/academy/${primarySession.slug}`);
  }

  const registerHref = primarySession
    ? `/academy/${primarySession.slug}`
    : "/contact";
  const priceLabel = primarySession?.formatted_price
    ?? (primarySession?.price != null
      ? `${primarySession.price} ${primarySession.currency ?? "USD"}`
      : "1 200 USD");

  return (
    <>
      <section className="relative overflow-hidden pt-12 pb-16 md:pt-20 md:pb-24">
        <div
          aria-hidden
          className="pointer-events-none absolute -right-32 -top-40 size-[520px] rounded-full opacity-50 blur-3xl"
          style={{
            background:
              "radial-gradient(closest-side, rgba(180,83,9,0.32), transparent)",
          }}
        />
        <Container className="relative">
          <div className="grid items-end gap-10 md:grid-cols-12">
            <div className="md:col-span-8">
              <Eyebrow>
                {primarySession?.title ?? "SDev Academy — Cohorte 2026"}
              </Eyebrow>
              <h1 className="font-display mt-6 text-5xl leading-[1.02] tracking-tight md:text-7xl lg:text-[5.5rem]">
                Devenir développeur produit{" "}
                <span className="italic text-academy">
                  à l&apos;ère de l&apos;IA.
                </span>
              </h1>
              <p className="mt-7 max-w-2xl text-lg text-muted leading-relaxed md:text-xl">
                Une formation intensive de 12 semaines, à Kinshasa, pour
                apprendre à concevoir, coder et lancer des produits modernes.
                Mentorat 1-1, projets réels, certificat à la sortie.
              </p>
              <div className="mt-10 flex flex-wrap gap-3">
                <Link
                  href={registerHref}
                  className="inline-flex h-12 items-center gap-2 rounded-full bg-academy px-6 text-base text-white transition-colors hover:bg-academy/90"
                >
                  S&apos;inscrire à la cohorte
                  <ArrowRight className="size-4" />
                </Link>
                <Link
                  href="#programme"
                  className="inline-flex h-12 items-center gap-2 rounded-full border border-line bg-bg-elev px-6 text-base text-ink hover:border-line-strong"
                >
                  Voir le programme
                </Link>
              </div>
            </div>
            <aside className="md:col-span-4">
              <div className="relative aspect-[4/5] w-full overflow-hidden rounded-[24px] border border-line">
                <Image
                  src={stock.academy.classroom}
                  alt="Cohorte SDev Academy en formation"
                  fill
                  priority
                  sizes="(min-width: 768px) 33vw, 100vw"
                  className="object-cover"
                />
                <div
                  aria-hidden
                  className="absolute inset-0 bg-gradient-to-t from-ink/60 via-transparent to-transparent"
                />
                <div className="absolute inset-x-5 bottom-5 text-white">
                  <p className="font-mono text-[0.7rem] uppercase tracking-[0.2em] text-white/70">
                    Cohorte 03 · 2025
                  </p>
                  <p className="font-display mt-1 text-xl tracking-tight">
                    Apprendre en construisant.
                  </p>
                </div>
              </div>
              <div className="card-lg mt-4 space-y-5 p-6">
                <Row label="Format" value={primarySession?.format ?? "Hybride — Kinshasa"} />
                <Row label="Durée" value="12 semaines" />
                <Row
                  label="Démarrage"
                  value={
                    primarySession?.start_date
                      ? new Date(primarySession.start_date).toLocaleDateString("fr-FR", {
                          month: "long",
                          year: "numeric",
                        })
                      : "Septembre 2026"
                  }
                />
                <Row label="Tarif" value={priceLabel} />
                <Row label="Langue" value="Français" />
              </div>
            </aside>
          </div>
        </Container>
      </section>

      <section className="border-y border-line bg-bg-elev py-20 md:py-24">
        <Container>
          <div className="grid gap-10 md:grid-cols-2">
            <div>
              <Eyebrow>Pour qui ?</Eyebrow>
              <h2 className="font-display mt-4 text-3xl tracking-tight md:text-4xl">
                Une cohorte choisie,
                <br />
                pas une promotion massive.
              </h2>
              <ul className="mt-8 space-y-3">
                {audience.map((a) => (
                  <li key={a} className="flex items-start gap-3 text-base text-ink-soft md:text-lg">
                    <CheckCircle2 className="mt-0.5 size-5 shrink-0 text-academy" />
                    {a}
                  </li>
                ))}
              </ul>
            </div>
            <div>
              <Eyebrow>Ce que vous allez savoir faire</Eyebrow>
              <h2 className="font-display mt-4 text-3xl tracking-tight md:text-4xl">
                Sortir prêt
                <br />
                à livrer un produit.
              </h2>
              <ul className="mt-8 space-y-3">
                {outcomes.map((a) => (
                  <li key={a} className="flex items-start gap-3 text-base text-ink-soft md:text-lg">
                    <CheckCircle2 className="mt-0.5 size-5 shrink-0 text-academy" />
                    {a}
                  </li>
                ))}
              </ul>
            </div>
          </div>
        </Container>
      </section>

      <section id="programme" className="py-24 md:py-32">
        <Container>
          <div className="max-w-2xl">
            <Eyebrow>Programme — {track.title}</Eyebrow>
            <h2 className="font-display mt-4 text-4xl leading-[1.05] tracking-tight md:text-5xl">
              Quatre modules.
              <br />
              <span className="italic">Un seul fil conducteur : livrer.</span>
            </h2>
            <p className="mt-6 max-w-xl text-base text-muted md:text-lg">
              {track.description}
            </p>
          </div>

          <div className="mt-14 grid gap-4 md:grid-cols-2 md:gap-5">
            {track.modules.map((m, i) => (
              <Reveal key={m.number} delay={i * 0.05}>
                <article className="card-lg flex h-full flex-col p-7 md:p-8">
                  <div className="flex items-center justify-between">
                    <span className="font-mono text-xs text-muted">
                      Module {m.number}
                    </span>
                    <span className="inline-flex items-center rounded-full border border-line bg-bg px-2.5 py-1 text-[0.72rem] text-ink-soft">
                      {m.duration}
                    </span>
                  </div>
                  <h3 className="font-display mt-6 text-3xl tracking-tight md:text-4xl">
                    {m.title}
                  </h3>
                  <ul className="mt-6 space-y-2 text-sm text-ink-soft">
                    {m.outline.map((o) => (
                      <li key={o} className="flex items-start gap-2">
                        <span className="mt-1 inline-block size-1.5 shrink-0 rounded-full bg-academy" />
                        {o}
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
          <div className="grid gap-4 md:grid-cols-2 md:gap-5">
            <div className="relative aspect-[4/3] overflow-hidden rounded-[24px] border border-line">
              <Image
                src={stock.academy.mentoring}
                alt="Session de mentorat 1-1"
                fill
                sizes="(min-width: 768px) 50vw, 100vw"
                className="object-cover"
              />
              <div
                aria-hidden
                className="absolute inset-0 bg-gradient-to-t from-ink/55 via-transparent to-transparent"
              />
              <div className="absolute inset-x-6 bottom-6 text-white">
                <p className="font-mono text-[0.7rem] uppercase tracking-[0.2em] text-white/70">
                  Mentorat 1-1
                </p>
                <p className="font-display mt-1 text-2xl tracking-tight md:text-3xl">
                  Un binôme par apprenant.
                </p>
              </div>
            </div>
            <div className="relative aspect-[4/3] overflow-hidden rounded-[24px] border border-line">
              <Image
                src={stock.academy.students}
                alt="Étudiants Academy en projet"
                fill
                sizes="(min-width: 768px) 50vw, 100vw"
                className="object-cover"
              />
              <div
                aria-hidden
                className="absolute inset-0 bg-gradient-to-t from-ink/55 via-transparent to-transparent"
              />
              <div className="absolute inset-x-6 bottom-6 text-white">
                <p className="font-mono text-[0.7rem] uppercase tracking-[0.2em] text-white/70">
                  Projet final
                </p>
                <p className="font-display mt-1 text-2xl tracking-tight md:text-3xl">
                  Un produit livré, démo publique.
                </p>
              </div>
            </div>
          </div>
        </Container>
      </section>

      <section className="border-y border-line bg-bg-elev py-24 md:py-28">
        <Container>
          <div className="grid items-start gap-12 md:grid-cols-12">
            <div className="md:col-span-5">
              <Eyebrow>Tarif & financement</Eyebrow>
              <h2 className="font-display mt-4 text-4xl leading-[1.05] tracking-tight md:text-5xl">
                Une école accessible,
                <br />
                <span className="italic">pas low-cost.</span>
              </h2>
              <p className="mt-6 max-w-md text-base text-muted leading-relaxed md:text-lg">
                Nous ne formons pas en masse. Chaque place est sélectionnée
                et accompagnée. Des bourses partielles existent pour les
                profils les plus motivés.
              </p>
            </div>
            <div className="md:col-span-7">
              <div className="grid gap-4 sm:grid-cols-2">
                <div className="card-lg p-7">
                  <p className="eyebrow">Tarif standard</p>
                  <p className="font-display mt-6 text-5xl tracking-tight md:text-6xl">
                    {priceLabel}
                  </p>
                  <p className="mt-3 text-sm text-muted">
                    Paiement en 1 ou 3 fois.
                  </p>
                </div>
                <div className="card-lg p-7">
                  <p className="eyebrow">Bourse partielle</p>
                  <p className="font-display mt-6 text-5xl tracking-tight md:text-6xl">
                    −40%
                  </p>
                  <p className="mt-3 text-sm text-muted">
                    Sur dossier de motivation.
                  </p>
                </div>
              </div>
            </div>
          </div>
        </Container>
      </section>

      <section className="py-24 md:py-32">
        <Container>
          <div className="max-w-2xl">
            <Eyebrow>Questions fréquentes</Eyebrow>
            <h2 className="font-display mt-4 text-4xl leading-[1.05] tracking-tight md:text-5xl">
              Tout ce que vous voulez savoir.
            </h2>
          </div>
          <div className="mt-12 max-w-3xl">
            <FAQ items={faqs} />
          </div>
        </Container>
      </section>

      <VideoShowcase
        eyebrow="Promotion 2025"
        title="L'Academy, en mouvement."
        description="Un aperçu d'une cohorte au travail — du cadrage à la démo finale."
        src={stock.video.src}
        poster={stock.academy.poster}
      />

      <CTA
        title="Prêt·e à rejoindre la prochaine cohorte ?"
        subtitle="Les inscriptions sont ouvertes. Quelques places encore disponibles."
        cta="S'inscrire"
        href={registerHref}
      />
    </>
  );
}

function Row({ label, value }: { label: string; value: string }) {
  return (
    <div className="flex items-center justify-between gap-3 border-b border-line pb-4 last:border-0 last:pb-0">
      <span className="eyebrow">{label}</span>
      <span className="text-sm font-medium text-ink">{value}</span>
    </div>
  );
}
