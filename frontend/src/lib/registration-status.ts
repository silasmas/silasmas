/**
 * Styles d'alerte alignés sur les badges Filament (RegistrationResource).
 */
export type RegistrationStatusTone = "success" | "warning" | "info" | "danger" | "gray";

/** Classes Tailwind par tonalité de statut. */
export const REGISTRATION_STATUS_STYLES: Record<RegistrationStatusTone, string> = {
  success: "border-emerald-600/25 bg-emerald-600/10 text-emerald-800 dark:text-emerald-300",
  warning: "border-amber-600/25 bg-amber-600/10 text-amber-900 dark:text-amber-200",
  info: "border-sky-600/25 bg-sky-600/10 text-sky-900 dark:text-sky-200",
  danger: "border-rose-600/25 bg-rose-600/10 text-rose-800 dark:text-rose-300",
  gray: "border-line bg-bg-elev text-muted",
};

/**
 * Retourne les classes CSS pour une alerte de retour paiement.
 *
 * @param payment Statut query (?payment=)
 * @return Classes Tailwind complètes
 */
export function getPaymentAlertStyle(payment: string): string {
  const tone: RegistrationStatusTone =
    payment === "success"
      ? "success"
      : payment === "cancel"
        ? "warning"
        : "danger";

  return REGISTRATION_STATUS_STYLES[tone];
}

/**
 * Retourne les classes CSS pour le statut d'inscription (API).
 *
 * @param status Statut registration (pending, confirmed, etc.)
 * @return Classes Tailwind complètes
 */
export function getRegistrationStatusStyle(status: string): string {
  const tone: RegistrationStatusTone =
    status === "confirmed"
      ? "success"
      : status === "pending" || status === "pending_payment"
        ? "warning"
        : status === "waitlist" || status === "pre_registered"
          ? "info"
          : status === "cancelled"
            ? "danger"
            : "gray";

  return REGISTRATION_STATUS_STYLES[tone];
}
