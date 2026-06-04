import type { MobileMoneyOperator } from "@/types/api";

const OPERATOR_PREFIXES: Record<MobileMoneyOperator, string[]> = {
  mpesa: ["81", "82", "83", "84", "85", "89"],
  airtel: ["97", "98", "99"],
  orange: ["80", "84", "85", "86", "87", "88", "89"],
  afrimoney: ["90", "91", "99"],
};

/**
 * Normalise un numéro RDC (243 + 9 chiffres).
 */
export function normalizeRdcPhone(phone: string): string | null {
  const digits = phone.replace(/\D/g, "");

  if (!digits) {
    return null;
  }

  let normalized = digits;

  if (normalized.startsWith("00")) {
    normalized = normalized.slice(2);
  }

  if (normalized.startsWith("0") && normalized.length === 10) {
    normalized = `243${normalized.slice(1)}`;
  }

  if (!normalized.startsWith("243") || normalized.length !== 12) {
    return null;
  }

  return normalized;
}

/**
 * Valide le numéro pour l'opérateur sélectionné.
 */
export function validateMobileMoneyPhone(
  phone: string,
  operator: MobileMoneyOperator
): { valid: boolean; normalized?: string; message?: string } {
  const normalized = normalizeRdcPhone(phone);

  if (!normalized) {
    return {
      valid: false,
      message:
        "Le numéro doit commencer par 243 et contenir 12 chiffres (ex. 243821234567).",
    };
  }

  const prefix = normalized.slice(3, 5);
  const allowed = OPERATOR_PREFIXES[operator] ?? [];

  if (!allowed.includes(prefix)) {
    const labels: Record<MobileMoneyOperator, string> = {
      mpesa: "M-Pesa (Vodacom)",
      airtel: "Airtel Money",
      orange: "Orange Money",
      afrimoney: "Afrimoney (Africell)",
    };

    return {
      valid: false,
      message: `Ce numéro ne correspond pas à ${labels[operator]}. Corrigez le numéro ou changez d'opérateur.`,
    };
  }

  return { valid: true, normalized };
}
