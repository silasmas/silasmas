import type {
  PaymentCurrency,
  PaymentCurrencyOption,
  SessionPaymentInfo,
  TrainingSession,
} from "@/types/api";

const PAYMENT_CURRENCIES: PaymentCurrency[] = ["USD", "CDF"];

/**
 * Indique si la session expose un tarif en USD et CDF.
 */
export function sessionHasDualPricing(session: TrainingSession): boolean {
  return session.price_usd != null && session.price_cdf != null;
}

/**
 * Formate un montant selon la devise.
 */
export function formatCurrencyAmount(amount: number, currency: string): string {
  if (currency === "CDF") {
    return `${amount.toLocaleString("fr-FR", { maximumFractionDigits: 0 })} CDF`;
  }

  return `${amount.toLocaleString("fr-FR", {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  })} ${currency}`;
}

/**
 * Construit une option de devise pour l'UI.
 */
function buildOption(currency: PaymentCurrency, amount: number): PaymentCurrencyOption {
  return {
    currency,
    amount,
    formatted: formatCurrencyAmount(amount, currency),
  };
}

/**
 * Options USD/CDF directement depuis la session API.
 */
function optionsFromSessionAmounts(session: TrainingSession): PaymentCurrencyOption[] {
  if (!sessionHasDualPricing(session)) {
    return [];
  }

  return [
    buildOption("USD", session.price_usd as number),
    buildOption("CDF", Math.round(session.price_cdf as number)),
  ];
}

/**
 * Calcule les montants USD et CDF à partir du tarif session et du taux.
 */
function computeDualAmounts(
  baseAmount: number,
  baseCurrency: string,
  rate: number | null | undefined
): { usd: number | null; cdf: number | null } {
  const currency = baseCurrency.toUpperCase();

  if (!rate || rate <= 0) {
    return {
      usd: currency === "USD" ? baseAmount : null,
      cdf: currency === "CDF" ? baseAmount : null,
    };
  }

  if (currency === "USD") {
    return {
      usd: Math.round(baseAmount * 100) / 100,
      cdf: Math.round(baseAmount * rate),
    };
  }

  if (currency === "CDF") {
    return {
      usd: Math.round((baseAmount / rate) * 100) / 100,
      cdf: Math.round(baseAmount),
    };
  }

  return { usd: null, cdf: null };
}

/**
 * Session payante (tolère is_paid absent si prix renseigné).
 */
function isPaidSession(
  session: TrainingSession,
  payment?: SessionPaymentInfo | null
): boolean {
  if (session.is_paid === true) {
    return true;
  }

  if (session.is_free === true) {
    return false;
  }

  return (session.price ?? payment?.amount ?? 0) > 0;
}

/**
 * Retourne les devises de paiement disponibles (même tarif, devise au choix).
 */
export function buildPaymentCurrencyOptions(
  session: TrainingSession,
  payment?: SessionPaymentInfo | null,
  exchangeRate?: number | null
): PaymentCurrencyOption[] {
  const fromSessionAmounts = optionsFromSessionAmounts(session);

  if (fromSessionAmounts.length >= 2) {
    return fromSessionAmounts;
  }

  const apiOptions =
    session.payment_currency_options?.length
      ? session.payment_currency_options
      : payment?.currency_options;

  if (apiOptions && apiOptions.length >= 2) {
    const filtered = apiOptions.filter((option) =>
      PAYMENT_CURRENCIES.includes(option.currency)
    );

    if (filtered.length >= 2) {
      return filtered;
    }
  }

  if (!isPaidSession(session, payment)) {
    return fromSessionAmounts;
  }

  const baseAmount = session.price ?? payment?.amount;

  if (baseAmount == null) {
    return fromSessionAmounts;
  }

  const baseCurrency = (session.currency ?? payment?.currency ?? "USD").toUpperCase();
  const rate = session.exchange_rate_usd_cdf ?? exchangeRate ?? null;

  let usd = session.price_usd ?? null;
  let cdf = session.price_cdf ?? null;

  if (usd == null || cdf == null) {
    const computed = computeDualAmounts(baseAmount, baseCurrency, rate);
    usd = usd ?? computed.usd;
    cdf = cdf ?? computed.cdf;
  }

  const options: PaymentCurrencyOption[] = [...fromSessionAmounts];

  if (usd != null && !options.some((option) => option.currency === "USD")) {
    options.push(buildOption("USD", usd));
  }

  if (cdf != null && !options.some((option) => option.currency === "CDF")) {
    options.push(buildOption("CDF", cdf));
  }

  if (options.length === 0) {
    const currency = baseCurrency as PaymentCurrency;
    options.push(buildOption(currency, baseAmount));
  }

  const seen = new Set<string>();

  return options.filter((option) => {
    if (seen.has(option.currency)) {
      return false;
    }

    seen.add(option.currency);
    return true;
  });
}

/**
 * Affiche le sélecteur USD / CDF.
 */
export function shouldShowCurrencyChoice(
  session: TrainingSession,
  options: PaymentCurrencyOption[]
): boolean {
  return options.length > 1 || sessionHasDualPricing(session);
}

/**
 * Libellé du montant sélectionné.
 */
export function formatSelectedPaymentTotal(
  option: PaymentCurrencyOption | undefined
): string {
  if (!option) {
    return "—";
  }

  return option.formatted ?? formatCurrencyAmount(option.amount, option.currency);
}
