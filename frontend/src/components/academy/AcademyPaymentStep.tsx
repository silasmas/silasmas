"use client";

import { FormEvent, useEffect, useMemo, useState } from "react";
import { useSiteSettings } from "@/components/providers/SiteSettingsProvider";
import {
  availableMobileOperatorOptions,
  enabledMobileOperators,
  enabledPaymentChannels,
} from "@/lib/academy-payment-channels";
import { checkAcademyPaymentStatus, processAcademyPayment } from "@/lib/api";
import { validateMobileMoneyPhone } from "@/lib/mobile-money";
import {
  buildPaymentCurrencyOptions,
  formatSelectedPaymentTotal,
  shouldShowCurrencyChoice,
} from "@/lib/payment-currency";
import {
  checkPaymentOnce,
  MAX_MANUAL_VERIFY_ATTEMPTS,
  pollPaymentAuto,
} from "@/lib/payment-polling";
import { REGISTRATION_STATUS_STYLES } from "@/lib/registration-status";
import type {
  MobileMoneyOperator,
  PaymentChannel,
  PaymentCurrency,
  SessionPaymentInfo,
  TrainingSession,
} from "@/types/api";

interface AcademyPaymentStepProps {
  /** Session de formation concernée */
  session: TrainingSession;
  /** Paiement en attente (référence FlexPay) */
  paymentInfo: SessionPaymentInfo;
  /** Téléphone prérempli (Mobile Money) */
  initialPhone?: string;
  /** Message d'information au-dessus du formulaire */
  resumeInfo?: string | null;
  /** Callback après paiement confirmé */
  onSuccess: () => void;
  /** Retour étape précédente (inscription multi-étapes) */
  onBack?: () => void;
  /** Affiche le bouton Retour */
  showBackButton?: boolean;
  /** Variante visuelle */
  variant?: "default" | "hero";
}

const inputClassDefault =
  "w-full rounded-xl border border-line bg-bg-elev px-4 py-3.5 text-ink outline-none transition-colors focus:border-accent";

const inputClassHero =
  "w-full rounded-xl border border-line bg-bg-elev px-5 py-4 text-base text-ink outline-none transition-colors focus:border-accent md:text-lg";

/**
 * Étape paiement Academy (Mobile Money / carte) — réutilisée inscription et finalisation.
 */
export function AcademyPaymentStep({
  session,
  paymentInfo,
  initialPhone = "",
  resumeInfo,
  onSuccess,
  onBack,
  showBackButton = true,
  variant = "default",
}: AcademyPaymentStepProps) {
  const siteSettings = useSiteSettings();
  const inputClass = variant === "hero" ? inputClassHero : inputClassDefault;

  const [loading, setLoading] = useState(false);
  const [polling, setPolling] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const [paymentCurrency, setPaymentCurrency] = useState<PaymentCurrency | "">("");
  const [channel, setChannel] = useState<PaymentChannel | "">("");
  const [mobileOperator, setMobileOperator] = useState<MobileMoneyOperator | "">("");
  const [phone, setPhone] = useState(initialPhone);
  const [termsAccepted, setTermsAccepted] = useState(false);
  const [showManualVerify, setShowManualVerify] = useState(false);
  const [verifyAttempts, setVerifyAttempts] = useState(0);

  const availablePaymentChannels = useMemo(
    () => enabledPaymentChannels(session),
    [
      session.payment_mobile_money_enabled,
      session.payment_card_enabled,
      session.enabled_mobile_operators,
    ]
  );

  const availableMobileOperators = useMemo(
    () => availableMobileOperatorOptions(session),
    [session.enabled_mobile_operators, session.payment_mobile_money_enabled]
  );

  const showPaymentMethodChoice = availablePaymentChannels.length > 1;
  const showMobileOperatorChoice = availableMobileOperators.length > 1;

  const currencyOptions = useMemo(
    () =>
      buildPaymentCurrencyOptions(
        session,
        paymentInfo,
        siteSettings.usd_to_cdf_rate
      ),
    [paymentInfo, session, siteSettings.usd_to_cdf_rate]
  );

  const showCurrencyChoice = shouldShowCurrencyChoice(session, currencyOptions);

  const selectedCurrencyOption = currencyOptions.find(
    (option) => option.currency === paymentCurrency
  );

  useEffect(() => {
    setPhone(initialPhone);
  }, [initialPhone]);

  useEffect(() => {
    if (availablePaymentChannels.length !== 1) {
      return;
    }

    const onlyChannel = availablePaymentChannels[0];

    if (channel !== onlyChannel) {
      setChannel(onlyChannel);

      if (onlyChannel === "card") {
        setMobileOperator("");
      }
    }
  }, [availablePaymentChannels, channel]);

  useEffect(() => {
    if (currencyOptions.length === 0) {
      return;
    }

    const defaultCurrency = currencyOptions[0].currency;

    if (
      paymentCurrency === "" ||
      !currencyOptions.some((option) => option.currency === paymentCurrency)
    ) {
      setPaymentCurrency(defaultCurrency);
    }
  }, [currencyOptions, paymentCurrency]);

  useEffect(() => {
    if (channel !== "mobile_money" || availableMobileOperators.length !== 1) {
      return;
    }

    const onlyOperator = availableMobileOperators[0].id;

    if (mobileOperator !== onlyOperator) {
      setMobileOperator(onlyOperator);
    }
  }, [channel, availableMobileOperators, mobileOperator]);

  /**
   * Confirme le paiement après polling ou relance manuelle.
   */
  async function runPaymentConfirmation(reference: string): Promise<boolean> {
    const result = await checkPaymentOnce(reference);

    if (result.confirmed) {
      setShowManualVerify(false);
      setError(null);
      onSuccess();
      return true;
    }

    if (result.cancelled) {
      setError(result.message || "Paiement annulé.");
      return false;
    }

    return false;
  }

  /** Relance manuelle si débit sans confirmation automatique. */
  async function handleManualVerify() {
    if (!paymentInfo.reference) {
      return;
    }

    if (verifyAttempts >= MAX_MANUAL_VERIFY_ATTEMPTS) {
      setError(
        "Nombre maximum de vérifications atteint. Contactez le support avec votre référence."
      );
      return;
    }

    setLoading(true);
    setError(null);
    setVerifyAttempts((attemptCount) => attemptCount + 1);

    try {
      const response = await checkAcademyPaymentStatus(paymentInfo.reference);
      const data = response.data;

      if (data?.confirmed || (data?.reponse && data.status === 0)) {
        onSuccess();
        setShowManualVerify(false);
        return;
      }

      if (data?.reponse === false && data.status === 1) {
        setError(data.message || "Paiement annulé.");
        return;
      }

      setError(
        `Paiement non encore confirmé (tentative ${verifyAttempts + 1}/${MAX_MANUAL_VERIFY_ATTEMPTS}). ` +
          "Attendez quelques secondes si vous venez de valider sur votre téléphone."
      );
      setShowManualVerify(true);
    } catch {
      setError("Impossible de vérifier le paiement pour le moment.");
    } finally {
      setLoading(false);
    }
  }

  /** Soumet le paiement (Mobile Money ou carte). */
  async function handlePayment(event: FormEvent) {
    event.preventDefault();
    setError(null);
    setShowManualVerify(false);
    setVerifyAttempts(0);

    if (!paymentInfo.reference) {
      setError("Référence de paiement manquante.");
      return;
    }

    if (!termsAccepted) {
      setError("Veuillez accepter les conditions générales.");
      return;
    }

    if (availablePaymentChannels.length === 0) {
      setError("Aucun moyen de paiement n'est disponible pour cette session.");
      return;
    }

    if (!channel || !availablePaymentChannels.includes(channel)) {
      setError("Choisissez un moyen de paiement.");
      return;
    }

    if (
      showCurrencyChoice &&
      currencyOptions.length > 1 &&
      (!paymentCurrency ||
        !currencyOptions.some((option) => option.currency === paymentCurrency))
    ) {
      setError("Choisissez la devise de paiement.");
      return;
    }

    if (channel === "mobile_money") {
      if (availableMobileOperators.length === 0) {
        setError("Aucun opérateur Mobile Money n'est disponible pour cette session.");
        return;
      }

      if (
        !mobileOperator ||
        !enabledMobileOperators(session).includes(mobileOperator)
      ) {
        setError("Choisissez votre opérateur Mobile Money.");
        return;
      }

      const phoneCheck = validateMobileMoneyPhone(phone, mobileOperator);

      if (!phoneCheck.valid) {
        setError(phoneCheck.message || "Numéro invalide.");
        return;
      }

      if (phoneCheck.normalized) {
        setPhone(phoneCheck.normalized);
      }
    }

    setLoading(true);

    try {
      const response = await processAcademyPayment({
        reference: paymentInfo.reference,
        channel,
        payment_currency:
          paymentCurrency ||
          (currencyOptions.length === 1 ? currencyOptions[0].currency : undefined),
        phone: channel === "mobile_money" ? phone.trim() : undefined,
        mobile_operator:
          channel === "mobile_money" && mobileOperator
            ? mobileOperator
            : undefined,
      });

      if (
        response.data?.type === "already_paid" ||
        (response.success && response.data?.reponse && response.data?.status === 0)
      ) {
        onSuccess();
        return;
      }

      if (!response.success || !response.data?.reponse) {
        setError(response.message || response.data?.message || "Échec du paiement.");
        return;
      }

      if (response.data.type === "card" && response.data.redirect_url) {
        window.location.href = response.data.redirect_url;
        return;
      }

      if (response.data.type === "mobile") {
        setPolling(true);
        const pollResult = await pollPaymentAuto(paymentInfo.reference);
        setPolling(false);

        if (pollResult.confirmed) {
          onSuccess();
          return;
        }

        if (pollResult.cancelled) {
          setError(pollResult.message || "Paiement annulé.");
          return;
        }

        const confirmed = await runPaymentConfirmation(paymentInfo.reference);

        if (!confirmed) {
          setShowManualVerify(true);
          setError(
            pollResult.message ||
              "Si votre compte a été débité, cliquez sur « Vérifier mon paiement » (3 tentatives)."
          );
        }
      }
    } catch {
      setError("Erreur réseau lors du paiement.");
    } finally {
      setLoading(false);
      setPolling(false);
    }
  }

  return (
    <form onSubmit={handlePayment} className="space-y-5">
      {resumeInfo && (
        <p
          className={`rounded-2xl border px-4 py-3 text-sm ${REGISTRATION_STATUS_STYLES.info}`}
        >
          {resumeInfo}
        </p>
      )}

      {error && (
        <p
          className={`rounded-2xl border px-4 py-3 text-sm ${REGISTRATION_STATUS_STYLES.danger}`}
        >
          {error}
        </p>
      )}

      <div className="space-y-3">
        {showCurrencyChoice && currencyOptions.length > 1 ? (
          <div>
            <p className="mb-2 text-sm font-medium text-ink">Devise de paiement *</p>
            <p className="mb-3 text-xs text-muted">
              Même tarif d&apos;inscription — choisissez la devise disponible sur
              votre compte.
            </p>
            <div className="grid gap-3 sm:grid-cols-2">
              {currencyOptions.map((option) => (
                <button
                  key={option.currency}
                  type="button"
                  className={`rounded-2xl border p-4 text-left transition ${
                    paymentCurrency === option.currency
                      ? "border-academy/50 bg-academy-soft"
                      : "border-line hover:border-academy/30"
                  }`}
                  onClick={() => setPaymentCurrency(option.currency)}
                >
                  <span className="font-semibold text-ink">{option.currency}</span>
                  <span className="mt-1 block text-sm text-academy">
                    {formatSelectedPaymentTotal(option)}
                  </span>
                </button>
              ))}
            </div>
          </div>
        ) : (
          <p className="text-sm text-ink-soft">
            Montant à payer :{" "}
            <strong className="text-academy">
              {formatSelectedPaymentTotal(
                selectedCurrencyOption ?? currencyOptions[0]
              )}
            </strong>
          </p>
        )}
        <p className="text-xs text-muted">
          Réf. {paymentInfo.reference}
          {showCurrencyChoice && selectedCurrencyOption && (
            <span className="mt-1 block sm:mt-0 sm:ml-2 sm:inline">
              Montant à débiter :{" "}
              <strong className="text-ink-soft">
                {formatSelectedPaymentTotal(selectedCurrencyOption)}
              </strong>
            </span>
          )}
        </p>
      </div>

      {availablePaymentChannels.length === 0 ? (
        <p className="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
          Le paiement en ligne n&apos;est pas configuré pour cette session. Contactez
          l&apos;équipe SDev Academy.
        </p>
      ) : showPaymentMethodChoice ? (
        <div>
          <p className="mb-2 text-sm font-medium text-ink">Moyen de paiement *</p>
          <div className="grid gap-3 sm:grid-cols-2">
            {availablePaymentChannels.includes("mobile_money") && (
              <button
                type="button"
                className={`rounded-2xl border p-4 text-left transition ${
                  channel === "mobile_money"
                    ? "border-academy/50 bg-academy-soft"
                    : "border-line hover:border-academy/30"
                }`}
                onClick={() => setChannel("mobile_money")}
              >
                <span className="font-semibold text-ink">Mobile Money</span>
                <span className="mt-1 block text-xs text-muted">
                  {availableMobileOperators.map((operator) => operator.label).join(", ")}
                </span>
              </button>
            )}
            {availablePaymentChannels.includes("card") && (
              <button
                type="button"
                className={`rounded-2xl border p-4 text-left transition ${
                  channel === "card"
                    ? "border-academy/50 bg-academy-soft"
                    : "border-line hover:border-academy/30"
                }`}
                onClick={() => {
                  setChannel("card");
                  setMobileOperator("");
                }}
              >
                <span className="font-semibold text-ink">Carte bancaire</span>
                <span className="mt-1 block text-xs text-muted">Visa, Mastercard</span>
              </button>
            )}
          </div>
        </div>
      ) : (
        <p className="text-sm text-ink-soft">
          Paiement par{" "}
          <strong className="text-ink">
            {availablePaymentChannels[0] === "mobile_money"
              ? "Mobile Money"
              : "carte bancaire"}
          </strong>
        </p>
      )}

      {channel === "mobile_money" && (
        <>
          {availableMobileOperators.length === 0 ? (
            <p className="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
              Aucun opérateur Mobile Money n&apos;est configuré pour cette session.
            </p>
          ) : showMobileOperatorChoice ? (
            <div>
              <p className="mb-2 text-sm font-medium text-ink">Opérateur *</p>
              <div
                className={`grid gap-2 ${
                  availableMobileOperators.length >= 4
                    ? "grid-cols-2 sm:grid-cols-4"
                    : availableMobileOperators.length === 3
                      ? "grid-cols-2 sm:grid-cols-3"
                      : "grid-cols-2"
                }`}
              >
                {availableMobileOperators.map((operatorOption) => (
                  <button
                    key={operatorOption.id}
                    type="button"
                    className={`rounded-xl border px-3 py-3 text-center text-sm transition ${
                      mobileOperator === operatorOption.id
                        ? "border-academy/50 bg-academy-soft text-academy"
                        : "border-line text-ink-soft hover:border-academy/30"
                    }`}
                    onClick={() => setMobileOperator(operatorOption.id)}
                  >
                    <span className="block font-medium">{operatorOption.label}</span>
                    <span className="text-xs opacity-70">{operatorOption.hint}</span>
                  </button>
                ))}
              </div>
            </div>
          ) : (
            <p className="text-sm text-ink-soft">
              Opérateur :{" "}
              <strong className="text-ink">
                {availableMobileOperators[0]?.label}
              </strong>
            </p>
          )}
          {availableMobileOperators.length > 0 && (
            <>
              <input
                required
                placeholder="Téléphone Mobile Money (243…) *"
                className={inputClass}
                value={phone}
                onChange={(event) => setPhone(event.target.value)}
              />
              <p className="text-xs text-muted">
                Le numéro doit commencer par 243 et correspondre à l&apos;opérateur
                choisi.
              </p>
            </>
          )}
        </>
      )}

      <label className="flex items-start gap-3 text-sm text-ink-soft">
        <input
          type="checkbox"
          checked={termsAccepted}
          onChange={(event) => setTermsAccepted(event.target.checked)}
          className="mt-1"
        />
        J&apos;accepte les conditions générales et la politique de paiement.
      </label>

      {polling && (
        <p
          className={`rounded-xl border px-4 py-3 text-sm ${REGISTRATION_STATUS_STYLES.warning}`}
        >
          Vérification du paiement en cours…
        </p>
      )}

      {showManualVerify && (
        <button
          type="button"
          className="btn btn-outline w-full"
          disabled={loading || verifyAttempts >= MAX_MANUAL_VERIFY_ATTEMPTS}
          onClick={handleManualVerify}
        >
          {loading
            ? "Vérification..."
            : `Vérifier mon paiement (${verifyAttempts}/${MAX_MANUAL_VERIFY_ATTEMPTS})`}
        </button>
      )}

      <div
        className={`flex gap-3 ${showBackButton ? "justify-between" : "justify-end"}`}
      >
        {showBackButton && onBack && (
          <button
            type="button"
            className="btn btn-outline"
            disabled={loading || polling}
            onClick={onBack}
          >
            Retour
          </button>
        )}
        <button
          type="submit"
          className="btn btn-gold btn-lg"
          disabled={loading || polling || availablePaymentChannels.length === 0}
        >
          {loading || polling ? "Traitement..." : "Payer maintenant"}
        </button>
      </div>
    </form>
  );
}
