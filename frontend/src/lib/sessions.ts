import type { TrainingSession } from "@/types/api";

/**
 * Choisit la session à mettre en avant (vedette parmi les ouvertes à venir, sinon la première).
 *
 * Écarte les sessions dont la date de fin est déjà passée : le statut "open" en base
 * n'est pas repassé automatiquement à "closed" une fois la formation terminée.
 *
 * @param sessions Liste des sessions ouvertes
 * @return Session principale ou null si aucune n'est encore à venir
 */
export function pickPrimarySession(
  sessions: TrainingSession[]
): TrainingSession | null {
  const upcoming = sessions.filter((session) => {
    const reference = session.end_date ?? session.start_date;
    if (!reference) return true;
    return new Date(reference.replace(" ", "T")).getTime() >= Date.now();
  });

  if (upcoming.length === 0) {
    return null;
  }

  return upcoming.find((session) => session.is_featured) ?? upcoming[0];
}
