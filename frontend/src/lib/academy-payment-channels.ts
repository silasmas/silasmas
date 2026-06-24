import type { MobileMoneyOperator, PaymentChannel, TrainingSession } from "@/types/api";

const ALL_MOBILE_OPERATORS: MobileMoneyOperator[] = [
  "mpesa",
  "airtel",
  "orange",
  "afrimoney",
];

export const MOBILE_OPERATOR_OPTIONS: {
  id: MobileMoneyOperator;
  label: string;
  hint: string;
}[] = [
  { id: "mpesa", label: "M-Pesa", hint: "Vodacom" },
  { id: "airtel", label: "Airtel Money", hint: "Airtel" },
  { id: "orange", label: "Orange Money", hint: "Orange" },
  { id: "afrimoney", label: "Afrimoney", hint: "Africell" },
];

/**
 * Retourne les opérateurs Mobile Money activés pour la session.
 *
 * @param session Session Academy
 * @return Liste des opérateurs disponibles
 */
export function enabledMobileOperators(
  session: TrainingSession
): MobileMoneyOperator[] {
  if (
    session.enabled_mobile_operators &&
    session.enabled_mobile_operators.length > 0
  ) {
    return session.enabled_mobile_operators.filter(
      (operator): operator is MobileMoneyOperator =>
        ALL_MOBILE_OPERATORS.includes(operator)
    );
  }

  if (session.payment_mobile_money_enabled === false) {
    return [];
  }

  return ALL_MOBILE_OPERATORS;
}

/**
 * Retourne les canaux de paiement activés pour la session.
 *
 * @param session Session Academy
 * @return Canaux (mobile_money, card)
 */
export function enabledPaymentChannels(
  session: TrainingSession
): PaymentChannel[] {
  const channels: PaymentChannel[] = [];

  if (enabledMobileOperators(session).length > 0) {
    channels.push("mobile_money");
  }

  if (session.payment_card_enabled !== false) {
    channels.push("card");
  }

  return channels;
}

/**
 * Filtre les opérateurs affichables dans l'UI.
 *
 * @param session Session Academy
 * @return Options opérateur avec libellés
 */
export function availableMobileOperatorOptions(session: TrainingSession) {
  return MOBILE_OPERATOR_OPTIONS.filter((operator) =>
    enabledMobileOperators(session).includes(operator.id)
  );
}
