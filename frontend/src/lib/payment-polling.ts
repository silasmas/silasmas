import { checkAcademyPaymentStatus } from "@/lib/api";

export const POLL_INTERVAL_MS = 2000;
export const AUTO_POLL_COUNT = 12;
export const MAX_MANUAL_VERIFY_ATTEMPTS = 3;

export interface PollPaymentResult {
  confirmed: boolean;
  cancelled: boolean;
  message?: string;
}

/**
 * Vérifie une fois le statut du paiement auprès de l'API.
 */
export async function checkPaymentOnce(reference: string): Promise<PollPaymentResult> {
  const res = await checkAcademyPaymentStatus(reference);
  const data = res.data;

  if (data?.confirmed || (data?.reponse && data.status === 0)) {
    return { confirmed: true, cancelled: false, message: res.message || data.message };
  }

  if (data?.reponse === false && data.status === 1) {
    return {
      confirmed: false,
      cancelled: true,
      message: data.message || res.message || "Paiement annulé",
    };
  }

  return {
    confirmed: false,
    cancelled: false,
    message: data?.message || res.message,
  };
}

/**
 * Polling automatique rapide après initiation Mobile Money.
 */
export async function pollPaymentAuto(reference: string): Promise<PollPaymentResult> {
  const immediate = await checkPaymentOnce(reference);

  if (immediate.confirmed || immediate.cancelled) {
    return immediate;
  }

  for (let i = 0; i < AUTO_POLL_COUNT; i += 1) {
    await new Promise((r) => setTimeout(r, POLL_INTERVAL_MS));
    const result = await checkPaymentOnce(reference);

    if (result.confirmed || result.cancelled) {
      return result;
    }
  }

  return {
    confirmed: false,
    cancelled: false,
    message:
      "Confirmation en cours. Si vous avez été débité, utilisez le bouton ci-dessous.",
  };
}
