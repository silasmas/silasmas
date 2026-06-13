"use client";

import Link from "next/link";
import { useRouter, useSearchParams } from "next/navigation";
import { RichHtmlContent } from "@/components/site/RichHtmlContent";
import { Suspense, useCallback, useEffect, useState } from "react";
import { ParticipantCountdown } from "@/components/academy/ParticipantCountdown";
import { SessionResourcesPanel } from "@/components/academy/SessionResourcesPanel";
import {
  acceptParticipantConfidentiality,
  getParticipantSpace,
} from "@/lib/api";
import { REGISTRATION_STATUS_STYLES } from "@/lib/registration-status";
import type { ParticipantSpace } from "@/types/api";

interface ParticipantSpaceViewProps {
  token: string;
}

interface ProfileField {
  label: string;
  value: string;
}

/**
 * Construit les champs profil affichables (non vides).
 *
 * @param student Données étudiant API
 * @return Liste de champs avec valeur
 */
function buildProfileFields(student: ParticipantSpace["student"]): ProfileField[] {
  const candidates: ProfileField[] = [
    { label: "E-mail", value: student.email?.trim() ?? "" },
    { label: "Téléphone", value: student.phone?.trim() ?? "" },
    { label: "Ville", value: student.city?.trim() ?? "" },
    { label: "Pays", value: student.country?.trim() ?? "" },
    { label: "Niveau d'études", value: student.education_level?.trim() ?? "" },
  ];

  return candidates.filter((field) => field.value.length > 0);
}

/**
 * Contenu de l'espace participant (chargement client).
 */
function ParticipantSpaceContent({ token }: ParticipantSpaceViewProps) {
  const router = useRouter();
  const searchParams = useSearchParams();
  const [data, setData] = useState<ParticipantSpace | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  const paymentSuccess = searchParams.get("payment") === "success";

  const load = useCallback(async () => {
    setLoading(true);
    setError(null);
    const space = await getParticipantSpace(token);

    if (!space) {
      setError("Accès introuvable ou inscription non confirmée.");
      setData(null);
    } else {
      setData(space);
    }

    setLoading(false);
  }, [token]);

  useEffect(() => {
    load();
  }, [load]);

  if (loading) {
    return (
      <div className="card-lg rounded-2xl p-10 text-center text-muted">
        Chargement de votre espace…
      </div>
    );
  }

  if (error || !data) {
    return (
      <div className="card-lg rounded-2xl p-10 text-center">
        <p className={`rounded-xl border px-4 py-3 text-sm ${REGISTRATION_STATUS_STYLES.danger}`}>
          {error ?? "Accès impossible"}
        </p>
        <Link href="/" className="btn btn-outline mt-6 inline-block">
          Retour à l&apos;accueil
        </Link>
      </div>
    );
  }

  const startLabel = new Date(data.session.start_date).toLocaleDateString("fr-FR", {
    day: "numeric",
    month: "long",
    year: "numeric",
  });

  const formatLabel =
    data.session.format === "online"
      ? "En ligne"
      : data.session.format === "onsite"
        ? "Présentiel"
        : "Hybride";

  const profileFields = buildProfileFields(data.student);

  return (
    <div className="space-y-8">
      {paymentSuccess && (
        <div className={`rounded-2xl border px-4 py-3 text-sm ${REGISTRATION_STATUS_STYLES.success}`}>
          Paiement confirmé. Bienvenue dans votre espace formation.
        </div>
      )}

      <div className="card-lg rounded-2xl p-6 md:p-8">
        <span className="eyebrow">Mon espace formation</span>
        <h1 className="font-display mt-2 text-3xl tracking-tight text-ink md:text-4xl">
          Bonjour {data.student.firstname} {data.student.lastname}
        </h1>
        <p className="mt-2 text-lg font-medium text-academy">{data.session.title}</p>
        {data.session.subtitle && (
          <p className="text-muted">{data.session.subtitle}</p>
        )}
        <p className="mt-2 text-sm text-ink-soft">
          {startLabel} — {formatLabel}
        </p>
      </div>

      <ParticipantCountdown
        targetIso={data.countdown_target}
        startDateLabel={startLabel}
      />

      {profileFields.length > 0 && (
        <div className="card-lg rounded-2xl p-6 md:p-7">
          <h2 className="font-display mb-4 text-xl tracking-tight text-ink">
            Vos informations
          </h2>
          <dl className="grid gap-4 text-sm sm:grid-cols-2">
            {profileFields.map((field) => (
              <div key={field.label}>
                <dt className="eyebrow mb-1">{field.label}</dt>
                <dd className="text-base text-ink">{field.value}</dd>
              </div>
            ))}
          </dl>
        </div>
      )}

      {data.session.participant_benefits?.trim() && (
        <div className="card-lg rounded-2xl p-6 md:p-7">
          <h2 className="font-display mb-3 text-xl tracking-tight text-ink">
            Ce qui vous attend
          </h2>
          <RichHtmlContent
            html={data.session.participant_benefits}
            className="text-sm text-ink-soft"
          />
        </div>
      )}

      {data.session.resources.length > 0 && (
        <SessionResourcesPanel
          resources={data.session.resources}
          confidentialityNotice={data.session.confidentiality_notice ?? ""}
          onAccepted={() => {
            if (!data.registration.confidentiality_accepted) {
              acceptParticipantConfidentiality(token);
            }
          }}
        />
      )}

      <div className="flex flex-wrap gap-3">
        <Link href={`/academy/${data.session.slug}`} className="btn btn-outline">
          Page de la session
        </Link>
        <button
          type="button"
          className="btn btn-gold"
          onClick={() => router.push("/")}
        >
          Retour à l&apos;accueil
        </button>
      </div>
    </div>
  );
}

/**
 * Espace participant avec Suspense pour les paramètres d'URL.
 */
export function ParticipantSpaceView({ token }: ParticipantSpaceViewProps) {
  return (
    <Suspense
      fallback={
        <div className="card-lg rounded-2xl p-10 text-center text-muted">
          Chargement…
        </div>
      }
    >
      <ParticipantSpaceContent token={token} />
    </Suspense>
  );
}
