import { PreRegistrationForm } from "@/components/academy/PreRegistrationForm";
import { RegistrationOpensCountdown } from "@/components/academy/RegistrationOpensCountdown";
import { SessionPoster } from "@/components/academy/SessionPoster";
import { SessionRegistrationBenefits } from "@/components/academy/SessionRegistrationBenefits";
import { RichHtmlContent } from "@/components/site/RichHtmlContent";
import { RichHtmlReadMore } from "@/components/site/RichHtmlReadMore";
import { Container } from "@/components/site/Container";
import { Eyebrow } from "@/components/site/Eyebrow";
import type { TrainingSession } from "@/types/api";

interface PreRegistrationViewProps {
  session: TrainingSession;
}

/**
 * Formate une date ISO en libellé français court.
 */
function formatSessionDate(isoDate: string): string {
  return new Date(`${isoDate}T12:00:00`).toLocaleDateString("fr-FR", {
    day: "numeric",
    month: "long",
    year: "numeric",
  });
}

/**
 * Libellé du format de session.
 */
function formatLabel(value: string): string {
  if (value === "online") {
    return "En ligne";
  }

  if (value === "onsite") {
    return "Présentiel";
  }

  if (value === "hybrid") {
    return "Hybride";
  }

  return value;
}

/**
 * Page de pré-inscription : affiche, compte à rebours, infos et formulaire léger.
 */
export function PreRegistrationView({ session }: PreRegistrationViewProps) {
  const opensAt = session.registration_opens_at;
  const opensLabel =
    session.registration_opens_at_label
    ?? (opensAt ? new Date(opensAt).toLocaleString("fr-FR") : "");

  return (
    <section className="py-10 md:py-16 lg:py-20">
      <Container size="wide">
        <div className="mb-6 md:mb-8">
          <Eyebrow>SDev Academy — Pré-inscription</Eyebrow>
          <h1 className="font-display mt-4 max-w-4xl text-3xl leading-[1.05] tracking-tight md:text-5xl lg:text-[3.25rem]">
            {session.title}
          </h1>
          {session.subtitle && (
            <p className="mt-4 max-w-3xl text-lg text-muted md:text-xl">{session.subtitle}</p>
          )}
          {session.pre_registration_message && (
            <div className="mt-5 max-w-3xl rounded-2xl border border-academy/20 bg-academy-soft/30 px-5 py-4 md:text-lg">
              <RichHtmlContent
                html={session.pre_registration_message}
                className="text-base text-ink"
              />
            </div>
          )}
        </div>

        <div className="grid gap-8 lg:grid-cols-[minmax(260px,380px)_minmax(0,1fr)] lg:items-start lg:gap-10 xl:gap-14">
          <aside className="space-y-6 lg:sticky lg:top-24">
            <SessionPoster
              session={session}
              priority
              variant="hero"
              preferPreRegistrationCover
              className="w-full shadow-[0_24px_64px_rgba(0,31,63,0.18)]"
            />

            <div className="card-lg space-y-3 rounded-2xl p-5 md:p-6">
              <h2 className="font-display text-xl tracking-tight">Informations</h2>
              <dl className="space-y-2 text-sm">
                <div className="flex justify-between gap-4 border-b border-line/60 pb-2">
                  <dt className="text-muted">Début</dt>
                  <dd className="font-medium">{formatSessionDate(session.start_date)}</dd>
                </div>
                <div className="flex justify-between gap-4 border-b border-line/60 pb-2">
                  <dt className="text-muted">Fin</dt>
                  <dd className="font-medium">{formatSessionDate(session.end_date)}</dd>
                </div>
                <div className="flex justify-between gap-4 border-b border-line/60 pb-2">
                  <dt className="text-muted">Format</dt>
                  <dd className="font-medium">{formatLabel(session.format)}</dd>
                </div>
                <div className="flex justify-between gap-4">
                  <dt className="text-muted">Tarif</dt>
                  <dd className="font-medium">
                    {session.is_free || !session.is_paid
                      ? "Gratuit"
                      : session.formatted_price ?? `${session.price} ${session.currency}`}
                  </dd>
                </div>
              </dl>
            </div>
          </aside>

          <div className="min-w-0 space-y-8">
            {opensAt && (
              <RegistrationOpensCountdown
                targetIso={opensAt}
                targetLabel={opensLabel}
              />
            )}

            <PreRegistrationForm session={session} />

            <SessionRegistrationBenefits benefits={session.registration_benefits ?? []} />
          </div>
        </div>

        {(session.description || session.program) && (
          <div className="mt-14 grid gap-8 lg:grid-cols-2">
            {session.description && (
              <div className="card-lg p-6 md:p-7">
                <h2 className="font-display mb-3 text-2xl tracking-tight">Description</h2>
                <RichHtmlReadMore
                  html={session.description}
                  className="text-muted"
                  variant="card"
                />
              </div>
            )}
            {session.program && (
              <div className="card-lg p-6 md:p-7">
                <h2 className="font-display mb-3 text-2xl tracking-tight">Programme</h2>
                <RichHtmlReadMore
                  html={session.program}
                  className="text-sm text-muted"
                  variant="card"
                />
              </div>
            )}
          </div>
        )}
      </Container>
    </section>
  );
}
