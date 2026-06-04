import type { TrainingSession } from "@/types/api";

/**
 * Choisit la session à mettre en avant (vedette parmi les ouvertes, sinon première ouverte).
 *
 * @param sessions Liste des sessions ouvertes
 * @return Session principale ou null
 */
export function pickPrimarySession(
  sessions: TrainingSession[]
): TrainingSession | null {
  if (sessions.length === 0) {
    return null;
  }

  return sessions.find((session) => session.is_featured) ?? sessions[0];
}
