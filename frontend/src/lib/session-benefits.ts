import type { TrainingSession } from "@/types/api";

/**
 * Extrait le libellé d'un avantage (chaîne ou objet Filament).
 *
 * @param item Valeur brute
 * @return Libellé nettoyé ou chaîne vide
 */
function normalizeBenefitItem(item: unknown): string {
  if (typeof item === "string") {
    return item.trim();
  }

  if (item && typeof item === "object") {
    const record = item as Record<string, unknown>;
    const candidate = record.benefit ?? record.label ?? record.value ?? Object.values(record)[0];

    if (typeof candidate === "string") {
      return candidate.trim();
    }
  }

  return "";
}

/**
 * Normalise la liste d'avantages d'inscription renvoyée par l'API.
 *
 * @param session Session de formation
 * @return Libellés non vides
 */
export function getRegistrationBenefits(session: TrainingSession): string[] {
  const raw = session.registration_benefits;

  if (!Array.isArray(raw)) {
    return [];
  }

  return raw
    .map(normalizeBenefitItem)
    .filter((item) => item.length > 0);
}
