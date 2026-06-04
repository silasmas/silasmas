import { AcademySectionClient } from "@/components/sections/AcademySectionClient";
import { pickPrimarySession } from "@/lib/sessions";
import type { TrainingSession } from "@/types/api";

interface AcademySectionProps {
  sessions: TrainingSession[];
}

/**
 * Section mise en avant SDev Academy sur la page d'accueil.
 */
export function AcademySection({ sessions }: AcademySectionProps) {
  const featured = pickPrimarySession(sessions);

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
          <AcademySectionClient featured={featured} />
        ) : (
          <div className="glass rounded-[2rem] p-10 text-center">
            <p className="text-muted">
              Les prochaines sessions seront bientôt annoncées. Vérifiez dans
              l&apos;admin que le statut est « Inscriptions ouvertes ».
            </p>
          </div>
        )}
      </div>
    </section>
  );
}
