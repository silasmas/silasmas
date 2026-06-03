import Link from "next/link";
import type { TrainingSession } from "@/types/api";

interface AcademySectionProps {
  sessions: TrainingSession[];
}

/**
 * Formate une date en français court.
 */
function formatDate(date: string): string {
  return new Date(date).toLocaleDateString("fr-FR", {
    day: "numeric",
    month: "long",
    year: "numeric",
  });
}

/**
 * Section mise en avant SDev Academy sur la page d'accueil.
 */
export function AcademySection({ sessions }: AcademySectionProps) {
  const featured = sessions[0];

  return (
    <section id="academy" className="section">
      <div className="container">
        <div className="mb-12 text-center">
          <span className="section-eyebrow">Nouveau</span>
          <h2 className="section-title">
            SDev <span className="text-gold">Academy</span>
          </h2>
          <p className="section-subtitle mx-auto">
            La branche formation de Silas Développe — accompagnement et montée en
            compétences dans les métiers du numérique en RDC et en Afrique.
          </p>
        </div>

        {featured ? (
          <div className="glass overflow-hidden rounded-[2rem] border border-amber-500/20">
            <div className="grid lg:grid-cols-[1.2fr_1fr]">
              <div className="p-8 md:p-10">
                <p className="mb-3 text-sm uppercase tracking-widest text-amber-400">
                  Prochaine session · {featured.format === "online" ? "En ligne" : featured.format}
                </p>
                <h3 className="mb-4 text-2xl font-bold md:text-3xl">{featured.title}</h3>
                {featured.subtitle && (
                  <p className="mb-4 text-slate-300">{featured.subtitle}</p>
                )}
                <p className="mb-6 text-slate-400">{featured.description}</p>
                <p className="mb-8 text-sm text-amber-300">
                  {formatDate(featured.start_date)} — {formatDate(featured.end_date)}
                </p>
                <Link
                  href={`/academy/${featured.slug}`}
                  className="btn btn-gold btn-lg"
                >
                  S&apos;inscrire maintenant
                </Link>
              </div>
              <div className="flex items-center justify-center bg-gradient-to-br from-amber-500/10 to-orange-600/5 p-8">
                <div className="rounded-3xl border border-amber-500/20 bg-black/50 p-8 text-center">
                  <p className="text-5xl font-black text-gold">SD</p>
                  <p className="mt-2 text-lg font-semibold">Academy</p>
                  <p className="mt-4 text-sm text-slate-400">
                    Première édition · Juin 2026
                  </p>
                </div>
              </div>
            </div>
          </div>
        ) : (
          <div className="glass rounded-[2rem] p-10 text-center">
            <p className="text-slate-400">
              Les prochaines sessions seront bientôt annoncées.
            </p>
          </div>
        )}
      </div>
    </section>
  );
}
