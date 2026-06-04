"use client";

import { useState } from "react";
import { ConfidentialityModal } from "@/components/academy/ConfidentialityModal";
import type { SessionResourceItem } from "@/types/api";

interface SessionResourcesPanelProps {
  resources: SessionResourceItem[];
  confidentialityNotice: string;
  onAccepted?: () => void;
}

const DEFAULT_NOTICE =
  "Les ressources de cette formation sont strictement confidentielles. " +
  "Elles sont réservées aux participants inscrits. Toute reproduction, diffusion " +
  "ou partage non autorisé est interdite.";

/**
 * Liste des ressources avec modale de confidentialité au clic.
 */
export function SessionResourcesPanel({
  resources,
  confidentialityNotice,
  onAccepted,
}: SessionResourcesPanelProps) {
  const [pendingResource, setPendingResource] = useState<SessionResourceItem | null>(null);
  const [hasAccepted, setHasAccepted] = useState(false);

  if (!resources.length) {
    return null;
  }

  const notice = confidentialityNotice?.trim() || DEFAULT_NOTICE;

  /**
   * Ouvre la modale ou le lien selon l'acceptation déjà donnée.
   */
  function handleResourceClick(resource: SessionResourceItem) {
    if (hasAccepted) {
      window.open(resource.url, "_blank", "noopener,noreferrer");
      return;
    }

    setPendingResource(resource);
  }

  /**
   * Accepte la notice et ouvre la ressource.
   */
  function handleAccept() {
    if (!pendingResource) {
      return;
    }

    setHasAccepted(true);
    onAccepted?.();
    window.open(pendingResource.url, "_blank", "noopener,noreferrer");
    setPendingResource(null);
  }

  return (
    <>
      <div className="glass rounded-3xl p-6">
        <h2 className="mb-2 text-xl font-semibold">Ressources</h2>
        <p className="mb-4 text-sm text-slate-400">
          Accès réservé — une notice de confidentialité s&apos;affiche avant l&apos;ouverture.
        </p>
        <ul className="space-y-3">
          {resources.map((resource, index) => (
            <li key={`${resource.url}-${index}`}>
              <button
                type="button"
                onClick={() => handleResourceClick(resource)}
                className="w-full rounded-2xl border border-white/10 bg-white/[0.03] px-4 py-3 text-left transition hover:border-amber-500/35"
              >
                <span className="font-medium text-amber-200">{resource.title}</span>
                {resource.description && (
                  <span className="mt-1 block text-xs text-slate-400">
                    {resource.description}
                  </span>
                )}
              </button>
            </li>
          ))}
        </ul>
      </div>

      <ConfidentialityModal
        open={pendingResource !== null}
        title="Confidentialité des ressources"
        notice={notice}
        resourceTitle={pendingResource?.title}
        onAccept={handleAccept}
        onClose={() => setPendingResource(null)}
      />
    </>
  );
}
