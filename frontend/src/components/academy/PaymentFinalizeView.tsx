"use client";

import Link from "next/link";
import { useEffect, useState } from "react";
import { AcademyPaymentStep } from "@/components/academy/AcademyPaymentStep";
import { SessionPoster } from "@/components/academy/SessionPoster";
import { Container } from "@/components/site/Container";
import { Eyebrow } from "@/components/site/Eyebrow";
import { buildParticipantUrl, getRegistrationResume } from "@/lib/api";
import { REGISTRATION_STATUS_STYLES } from "@/lib/registration-status";
import type {
  RegistrationFormPrefill,
  SessionPaymentInfo,
  TrainingSession,
} from "@/types/api";

interface PaymentFinalizeViewProps {
  /** Session de formation */
  session: TrainingSession;
  /** Jeton d'accès inscription (URL e-mail) */
  accessToken: string;
}

/**
 * Affiche le montant formaté de la session.
 */
function formatSessionPrice(session: TrainingSession): string | null {
  if (!session.is_paid) {
    return null;
  }

  if (session.formatted_price) {
    return session.formatted_price;
  }

  if (session.price_usd != null && session.price_cdf != null) {
    return `${session.price_usd.toLocaleString("fr-FR")} USD (${session.price_cdf.toLocaleString("fr-FR")} CDF)`;
  }

  if (session.price != null) {
    return `${session.price} ${session.currency ?? "USD"}`;
  }

  return null;
}

/**
 * Page de finalisation : récapitulatif participant + formulaire de paiement uniquement.
 */
export function PaymentFinalizeView({
  session,
  accessToken,
}: PaymentFinalizeViewProps) {
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [form, setForm] = useState<RegistrationFormPrefill | null>(null);
  const [paymentInfo, setPaymentInfo] = useState<SessionPaymentInfo | null>(null);
  const [participantToken, setParticipantToken] = useState<string | null>(null);
  const [completed, setCompleted] = useState(false);
  const [alreadyRegistered, setAlreadyRegistered] = useState(false);

  const priceLabel = formatSessionPrice(session);
  const safeToken = decodeURIComponent(accessToken).replace(/\*+$/g, "").trim();

  useEffect(() => {
    let cancelled = false;

    async function loadResume() {
      setLoading(true);
      setError(null);

      const result = await getRegistrationResume(safeToken);

      if (cancelled) {
        return;
      }

      if (!result.success || !result.data) {
        setError(result.message || "Lien invalide ou expiré.");
        setLoading(false);
        return;
      }

      const data = result.data;

      if (data.session_slug !== session.slug) {
        setError("Ce lien correspond à une autre session de formation.");
        setLoading(false);
        return;
      }

      setForm(data.form);

      const token =
        data.access_token ?? data.registration?.access_token ?? null;

      if (token) {
        setParticipantToken(token);
      }

      if (data.resume_action === "participant_space" || data.is_paid) {
        setAlreadyRegistered(true);
        setLoading(false);
        return;
      }

      if (
        data.resume_action === "payment" &&
        data.requires_payment &&
        data.payment
      ) {
        setPaymentInfo(data.payment);
        setLoading(false);
        return;
      }

      setError("Aucun paiement en attente pour cette inscription.");
      setLoading(false);
    }

    void loadResume();

    return () => {
      cancelled = true;
    };
  }, [safeToken, session.slug]);

  return (
    <section className="py-10 md:py-16 lg:py-20">
      <Container size="wide">
        <div className="mb-6 md:mb-8">
          <Eyebrow>SDev Academy — Finalisation</Eyebrow>
          <h1 className="font-display mt-4 max-w-4xl text-3xl leading-[1.05] tracking-tight md:text-5xl">
            Finaliser mon inscription
          </h1>
          <p className="mt-4 max-w-3xl text-lg text-muted md:text-xl">
            {session.title}
          </p>
        </div>

        <div className="grid gap-8 lg:grid-cols-[minmax(260px,380px)_minmax(0,1fr)] lg:items-start lg:gap-10 xl:gap-14">
          <aside className="lg:sticky lg:top-24">
            <SessionPoster
              session={session}
              priority
              variant="hero"
              className="w-full shadow-[0_24px_64px_rgba(0,31,63,0.18)]"
            />
          </aside>

          <div className="min-w-0">
            <div className="card-lg p-8 shadow-[0_20px_60px_rgba(0,31,63,0.08)] md:p-10">
              {loading && (
                <p className="text-center text-muted">
                  Chargement de votre inscription…
                </p>
              )}

              {!loading && error && (
                <div className="space-y-4 text-center">
                  <p
                    className={`rounded-2xl border px-4 py-3 text-sm ${REGISTRATION_STATUS_STYLES.danger}`}
                  >
                    {error}
                  </p>
                  <Link href={`/academy/${session.slug}`} className="btn btn-outline">
                    Retour à la session
                  </Link>
                </div>
              )}

              {!loading && !error && alreadyRegistered && form && (
                <div className="space-y-6 text-center">
                  <p
                    className={`text-lg font-semibold ${REGISTRATION_STATUS_STYLES.success}`}
                  >
                    Inscription déjà confirmée
                  </p>
                  <p className="text-sm text-ink-soft">
                    Bonjour <strong className="text-ink">{form.firstname}</strong>,
                    votre place à « {session.title} » est confirmée.
                  </p>
                  {participantToken && (
                    <Link
                      href={buildParticipantUrl(participantToken)}
                      className="btn btn-gold btn-lg inline-flex"
                    >
                      Accéder à mon espace formation
                    </Link>
                  )}
                </div>
              )}

              {!loading && !error && completed && form && (
                <div className="space-y-6 text-center">
                  <p
                    className={`text-lg font-semibold ${REGISTRATION_STATUS_STYLES.success}`}
                  >
                    Paiement confirmé !
                  </p>
                  <p className="text-sm text-ink-soft">
                    Merci {form.firstname}. Votre inscription à « {session.title} » est
                    confirmée. Un e-mail a été envoyé à{" "}
                    <strong className="text-ink">{form.email}</strong>.
                  </p>
                  {participantToken && (
                    <Link
                      href={buildParticipantUrl(participantToken)}
                      className="btn btn-gold btn-lg inline-flex"
                    >
                      Accéder à mon espace formation
                    </Link>
                  )}
                </div>
              )}

              {!loading &&
                !error &&
                !completed &&
                !alreadyRegistered &&
                form &&
                paymentInfo && (
                  <div className="space-y-8">
                    <div>
                      <h2 className="font-display text-2xl tracking-tight md:text-3xl">
                        Vos informations
                      </h2>
                      <p className="mt-2 text-sm text-muted">
                        Vérifiez vos coordonnées puis procédez au paiement ci-dessous.
                      </p>
                    </div>

                    <div className="rounded-2xl border border-line bg-bg p-5 text-sm text-ink-soft md:p-6">
                      <p>
                        <strong className="text-ink">
                          {form.firstname} {form.lastname}
                        </strong>
                      </p>
                      <p className="mt-1">{form.email}</p>
                      {form.phone && <p className="mt-1">{form.phone}</p>}
                      {form.city && <p className="mt-1">{form.city}</p>}
                      {form.country && <p className="mt-1">{form.country}</p>}
                      {form.education_level && (
                        <p className="mt-1">Niveau : {form.education_level}</p>
                      )}
                      {form.occupation && (
                        <p className="mt-1">Profession : {form.occupation}</p>
                      )}
                      <p className="mt-4 font-medium text-academy">{session.title}</p>
                      {priceLabel && (
                        <p className="mt-2 font-semibold text-academy">
                          Montant : {priceLabel}
                        </p>
                      )}
                    </div>

                    <div>
                      <h2 className="font-display mb-4 text-2xl tracking-tight md:text-3xl">
                        Paiement
                      </h2>
                      <AcademyPaymentStep
                        session={session}
                        paymentInfo={paymentInfo}
                        initialPhone={form.phone ?? ""}
                        resumeInfo="Finalisez votre paiement pour confirmer votre place."
                        onSuccess={() => setCompleted(true)}
                        showBackButton={false}
                        variant="hero"
                      />
                    </div>
                  </div>
                )}
            </div>
          </div>
        </div>
      </Container>
    </section>
  );
}
