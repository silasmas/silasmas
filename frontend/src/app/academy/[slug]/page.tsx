import Link from "next/link";
import { notFound } from "next/navigation";
import { RegistrationForm } from "@/components/academy/RegistrationForm";
import { getSessionBySlug } from "@/lib/api";

interface AcademyPageProps {
  params: Promise<{ slug: string }>;
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
 * Page détail d'une session SDev Academy avec formulaire d'inscription.
 */
export default async function AcademySessionPage({ params }: AcademyPageProps) {
  const { slug } = await params;
  const session = await getSessionBySlug(slug);

  if (!session) {
    notFound();
  }

  return (
    <section className="section">
      <div className="container max-w-5xl">
        <Link href="/#academy" className="mb-8 inline-block text-sm text-amber-400 hover:underline">
          ← Retour à l&apos;accueil
        </Link>

        <div className="mb-10">
          <span className="section-eyebrow">SDev Academy</span>
          <h1 className="section-title">{session.title}</h1>
          {session.subtitle && <p className="mb-4 text-lg text-slate-300">{session.subtitle}</p>}
          <p className="text-amber-300">
            {formatDate(session.start_date)} — {formatDate(session.end_date)} ·{" "}
            {session.format === "online" ? "En ligne" : session.format}
          </p>
        </div>

        <div className="grid gap-10 lg:grid-cols-[1fr_1fr]">
          <div className="space-y-6">
            {session.description && (
              <div className="glass rounded-3xl p-6">
                <h2 className="mb-3 text-xl font-semibold">Description</h2>
                <p className="text-slate-300">{session.description}</p>
              </div>
            )}
            {session.program && (
              <div className="glass rounded-3xl p-6">
                <h2 className="mb-3 text-xl font-semibold">Programme</h2>
                <pre className="whitespace-pre-wrap font-sans text-sm text-slate-300">
                  {session.program}
                </pre>
              </div>
            )}
          </div>

          <RegistrationForm session={session} />
        </div>
      </div>
    </section>
  );
}
