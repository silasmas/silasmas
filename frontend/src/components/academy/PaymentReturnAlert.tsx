"use client";

import { useSearchParams } from "next/navigation";
import { getPaymentAlertStyle } from "@/lib/registration-status";

/**
 * Affiche un message après retour paiement carte via query string.
 */
export function PaymentReturnAlert() {
  const searchParams = useSearchParams();
  const payment = searchParams.get("payment");
  const reference = searchParams.get("reference");

  if (!payment) {
    return null;
  }

  const messages: Record<string, string> = {
    success: "Paiement confirmé. Votre inscription est validée.",
    cancel: "Paiement annulé. Vous pouvez réessayer ci-dessous.",
    decline: "Paiement refusé. Vérifiez vos informations ou choisissez un autre moyen.",
    error: "Une erreur est survenue lors du paiement.",
  };

  const text = messages[payment] ?? messages.error;
  const styleClass = getPaymentAlertStyle(payment);

  return (
    <div
      className={`mb-6 rounded-2xl border px-4 py-3 text-sm ${styleClass}`}
      role="status"
    >
      {text}
      {reference && payment === "success" && (
        <span className="mt-1 block text-xs opacity-80">Réf. {reference}</span>
      )}
    </div>
  );
}
