"use client";

import Link from "next/link";
import { useRouter, useSearchParams } from "next/navigation";
import { Suspense, useCallback, useEffect, useState } from "react";
import { ParticipantCountdown } from "@/components/academy/ParticipantCountdown";
import { SessionResourcesPanel } from "@/components/academy/SessionResourcesPanel";
import {
  acceptParticipantConfidentiality,
  getParticipantSpace,
} from "@/lib/api";
import type { ParticipantSpace } from "@/types/api";

interface ParticipantSpaceViewProps {
  token: string;
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
      <div className="glass rounded-3xl p-10 text-center text-slate-400">
        Chargement de votre espace…
      </div>
    );
  }

  if (error || !data) {
    return (
      <div className="glass rounded-3xl p-10 text-center">
        <p className="text-red-400">{error ?? "Accès impossible"}</p>
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

  return (
    <div className="space-y-8">
      {paymentSuccess && (
        <div className="rounded-2xl border border-green-500/30 bg-green-500/10 px-4 py-3 text-sm text-green-400">
          Paiement confirmé. Bienvenue dans votre espace formation.
        </div>
      )}

      <div className="glass rounded-3xl p-6 md:p-8">
        <span className="section-eyebrow">Mon espace formation</span>
        <h1 className="section-title mt-2">
          Bonjour {data.student.firstname} {data.student.lastname}
        </h1>
        <p className="mt-2 text-lg text-amber-200/90">{data.session.title}</p>
        {data.session.subtitle && (
          <p className="text-muted">{data.session.subtitle}</p>
        )}
        <p className="mt-2 text-sm text-slate-400">
          {startLabel} — {formatLabel}
        </p>
      </div>

      <ParticipantCountdown
        targetIso={data.countdown_target}
        startDateLabel={startLabel}
      />

      <div className="glass rounded-3xl p-6">
        <h2 className="mb-4 text-xl font-semibold">Vos informations</h2>
        <dl className="grid gap-3 text-sm sm:grid-cols-2">
          <div>
            <dt className="text-slate-500">E-mail</dt>
            <dd className="text-white">{data.student.email}</dd>
          </div>
          {data.student.phone && (
            <div>
              <dt className="text-slate-500">Téléphone</dt>
              <dd className="text-white">{data.student.phone}</dd>
            </div>
          )}
          {data.student.city && (
            <div>
              <dt className="text-slate-500">Ville</dt>
              <dd className="text-white">{data.student.city}</dd>
            </div>
          )}
          {data.student.country && (
            <div>
              <dt className="text-slate-500">Pays</dt>
              <dd className="text-white">{data.student.country}</dd>
            </div>
          )}
          {data.student.education_level && (
            <div className="sm:col-span-2">
              <dt className="text-slate-500">Niveau d&apos;études</dt>
              <dd className="text-white">{data.student.education_level}</dd>
            </div>
          )}
        </dl>
      </div>

      {data.session.participant_benefits && (
        <div className="glass rounded-3xl p-6">
          <h2 className="mb-3 text-xl font-semibold">Ce qui vous attend</h2>
          <p className="whitespace-pre-wrap text-sm text-slate-300">
            {data.session.participant_benefits}
          </p>
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
        <div className="glass rounded-3xl p-10 text-center text-slate-400">
          Chargement…
        </div>
      }
    >
      <ParticipantSpaceContent token={token} />
    </Suspense>
  );
}
