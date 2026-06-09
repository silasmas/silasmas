import Link from "next/link";
import { ArrowUpRight } from "lucide-react";
import { Container } from "./Container";
import { Eyebrow } from "./Eyebrow";
import type { TrainingSession } from "@/types/api";

/**
 * Bandeau Academy avec lien vers la session ouverte principale.
 */
export function AcademyTeaser({ session }: { session?: TrainingSession | null }) {
  const registerHref = session ? `/academy/${session.slug}` : "/academy";
  const cohortLabel = session?.title ?? "SDev Academy — Cohorte 2026";

  return (
    <section className="py-24 md:py-32">
      <Container>
        <div className="surface-strong relative overflow-hidden rounded-[32px] border border-transparent p-8 md:p-14 lg:p-20">
          <div
            aria-hidden
            className="pointer-events-none absolute -right-32 -top-32 size-[480px] rounded-full opacity-30 blur-3xl"
            style={{
              background:
                "radial-gradient(closest-side, rgba(255,91,31,0.6), transparent)",
            }}
          />
          <div
            aria-hidden
            className="pointer-events-none absolute -left-20 bottom-[-120px] size-[420px] rounded-full opacity-30 blur-3xl"
            style={{
              background:
                "radial-gradient(closest-side, rgba(180,83,9,0.55), transparent)",
            }}
          />

          <div className="relative grid gap-12 lg:grid-cols-12">
            <div className="lg:col-span-7">
              <Eyebrow className="text-[color:var(--strong-fg-soft)]">{cohortLabel}</Eyebrow>
              <h2 className="font-display mt-4 text-4xl leading-[1.05] tracking-tight md:text-6xl">
                Devenir développeur produit{" "}
                <span className="italic text-accent">à l&apos;ère de l&apos;IA.</span>
              </h2>
              <p className="mt-6 max-w-xl text-lg text-[color:var(--strong-fg-muted)] md:text-xl">
                12 semaines hybrides, un mentorat personnel et un projet réel.
                Une école pensée pour transformer la curiosité en carrière.
              </p>
              <div className="mt-10 flex flex-wrap gap-3">
                <Link
                  href={registerHref}
                  className="inline-flex h-12 items-center gap-2 rounded-full bg-accent px-6 text-base text-white transition-colors hover:bg-accent/90"
                >
                  {session ? "S'inscrire à la cohorte" : "Rejoindre la prochaine cohorte"}
                  <ArrowUpRight className="size-4" />
                </Link>
                <Link
                  href="/academy#programme"
                  className="inline-flex h-12 items-center gap-2 rounded-full border border-[color:var(--strong-line)] px-6 text-base hover:bg-[color:var(--strong-line)]"
                >
                  Voir le programme
                </Link>
              </div>
            </div>

            <div className="lg:col-span-5">
              <ul className="grid grid-cols-2 gap-3">
                {[
                  { v: "12", l: "Semaines" },
                  { v: "1-1", l: "Mentorat" },
                  { v: "FR", l: "Langue" },
                  { v: "100%", l: "Projet réel" },
                ].map((item) => (
                  <li
                    key={item.l}
                    className="rounded-2xl border border-[color:var(--strong-line)] bg-[color:var(--strong-line)] p-5"
                  >
                    <p className="eyebrow text-[color:var(--strong-fg-soft)]">{item.l}</p>
                    <p className="font-display mt-6 text-4xl tracking-tight md:text-5xl">
                      {item.v}
                    </p>
                  </li>
                ))}
              </ul>
            </div>
          </div>
        </div>
      </Container>
    </section>
  );
}
