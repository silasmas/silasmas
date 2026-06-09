"use client";

import { getRegistrationBenefits } from "@/lib/session-benefits";
import type { TrainingSession } from "@/types/api";

/**
 * Retourne les avantages d'inscription fournis par la session (données serveur).
 *
 * @param _slug Slug conservé pour compatibilité des appels existants
 * @param session Session chargée côté serveur ou modale
 * @return Liste d'avantages
 */
export function useRegistrationBenefits(
  _slug: string,
  session?: TrainingSession | null,
): string[] {
  return session ? getRegistrationBenefits(session) : [];
}
