"use client";

import { useSearchParams } from "next/navigation";

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

  const messages: Record<string, { text: string; className: string }> = {
    success: {
      text: "Paiement confirmé. Votre inscription est validée.",
      className: "text-green-400 border-green-500/30 bg-green-500/10",
    },
    cancel: {
      text: "Paiement annulé. Vous pouvez réessayer ci-dessous.",
      className: "text-amber-400 border-amber-500/30 bg-amber-500/10",
    },
    decline: {
      text: "Paiement refusé. Vérifiez vos informations ou choisissez un autre moyen.",
      className: "text-red-400 border-red-500/30 bg-red-500/10",
    },
    error: {
      text: "Une erreur est survenue lors du paiement.",
      className: "text-red-400 border-red-500/30 bg-red-500/10",
    },
  };

  const info = messages[payment] ?? messages.error;

  return (
    <div
      className={`mb-6 rounded-2xl border px-4 py-3 text-sm ${info.className}`}
      role="status"
    >
      {info.text}
      {reference && payment === "success" && (
        <span className="mt-1 block text-xs opacity-80">Réf. {reference}</span>
      )}
    </div>
  );
}
