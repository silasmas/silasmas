"use client";

import { useEffect, useState } from "react";
import { fetchRegistrationBenefits } from "@/lib/api";
import { getRegistrationBenefits } from "@/lib/session-benefits";
import type { TrainingSession } from "@/types/api";

/**
 * Charge les avantages d'inscription en direct depuis l'API (sans cache).
 *
 * @param slug Slug de la session
 * @param session Session initiale (rendu serveur)
 * @return Liste d'avantages à jour
 */
export function useRegistrationBenefits(
  slug: string,
  session?: TrainingSession | null,
): string[] {
  const [benefits, setBenefits] = useState<string[]>(() => {
    return session ? getRegistrationBenefits(session) : [];
  });

  useEffect(() => {
    let cancelled = false;

    fetchRegistrationBenefits(slug)
      .then((items) => {
        if (!cancelled) {
          setBenefits(items);
        }
      })
      .catch(() => {
        // Conserve les avantages initiaux en cas d'erreur réseau.
      });

    return () => {
      cancelled = true;
    };
  }, [slug]);

  return benefits;
}
