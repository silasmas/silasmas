import { notFound } from "next/navigation";
import { AcademySessionRegistration } from "@/components/academy/AcademySessionRegistration";
import { SessionMediaPanel } from "@/components/academy/SessionMediaPanel";
import { SessionResourcesPanel } from "@/components/academy/SessionResourcesPanel";
import { getSessionBySlug } from "@/lib/api";

interface AcademySessionLoaderProps {
  slug: string;
}

/**
 * Formate une date en français.
 */
function formatDate(date: string): string {
  return new Date(date).toLocaleDateString("fr-FR", {
    day: "numeric",
    month: "long",
    year: "numeric",
  });
}

/**
 * Charge et affiche le détail d'une session Academy.
 */
export async function AcademySessionLoader({ slug }: AcademySessionLoaderProps) {
  const session = await getSessionBySlug(slug);

  if (!session) {
    notFound();
  }

  return (
    <section className="section">
      <div className="container max-w-5xl">
        <div className="mb-10 grid gap-8 lg:grid-cols-[1fr_320px] lg:items-start">
          <div>
            <span className="section-eyebrow">SDev Academy</span>
            <h1 className="section-title">{session.title}</h1>
            {session.subtitle && (
              <p className="mb-4 text-lg text-muted">{session.subtitle}</p>
            )}
            <p className="text-accent">
              {formatDate(session.start_date)} — {formatDate(session.end_date)} ·{" "}
              {session.format === "online" ? "En ligne" : session.format}
              {session.is_paid && session.formatted_price && (
                <> · <span className="text-amber-300">{session.formatted_price}</span></>
              )}
              {session.is_free !== false && !session.is_paid && (
                <> · <span className="text-green-400">Gratuit</span></>
              )}
            </p>
          </div>
          <SessionMediaPanel session={session} priority />
        </div>

        <div className="grid gap-10 lg:grid-cols-[1fr_1fr]">
          <div className="space-y-6">
            {session.description && (
              <div className="glass rounded-3xl p-6">
                <h2 className="mb-3 text-xl font-semibold">Description</h2>
                <p className="text-muted">{session.description}</p>
              </div>
            )}
            {session.program && (
              <div className="glass rounded-3xl p-6">
                <h2 className="mb-3 text-xl font-semibold">Programme</h2>
                <pre className="whitespace-pre-wrap font-sans text-sm text-muted">
                  {session.program}
                </pre>
              </div>
            )}
            {session.session_resources && session.session_resources.length > 0 && (
              <SessionResourcesPanel
                resources={session.session_resources}
                confidentialityNotice={session.confidentiality_notice ?? ""}
              />
            )}
          </div>

          <AcademySessionRegistration session={session} />
        </div>
      </div>
    </section>
  );
}
