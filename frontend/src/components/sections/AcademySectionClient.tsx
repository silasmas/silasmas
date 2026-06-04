"use client";

import Link from "next/link";
import { SessionMediaPanel } from "@/components/academy/SessionMediaPanel";
import type { TrainingSession } from "@/types/api";

interface AcademySectionClientProps {
  featured: TrainingSession;
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
 * Carte session Academy avec médias, vidéo et partage.
 */
export function AcademySectionClient({ featured }: AcademySectionClientProps) {
  return (
    <div className="glass overflow-hidden rounded-[2rem] border border-[var(--color-border)]">
      <div className="grid lg:grid-cols-[1.2fr_1fr]">
        <div className="p-8 md:p-10">
          <p className="mb-3 text-sm uppercase tracking-widest text-accent">
            {featured.status === "open" ? "Inscriptions ouvertes" : "Session"} ·{" "}
            {featured.format === "online" ? "En ligne" : featured.format}
          </p>
          <h3 className="mb-4 text-2xl font-bold md:text-3xl">{featured.title}</h3>
          {featured.subtitle && <p className="mb-4 text-muted">{featured.subtitle}</p>}
          <p className="mb-6 text-muted">{featured.description}</p>
          <p className="mb-8 text-sm text-accent">
            {formatDate(featured.start_date)} — {formatDate(featured.end_date)}
          </p>
          <Link href={`/academy/${featured.slug}`} className="btn btn-gold btn-lg">
            S&apos;inscrire maintenant
          </Link>
        </div>
        <div className="p-6 md:p-8">
          <SessionMediaPanel session={featured} />
        </div>
      </div>
    </div>
  );
}
