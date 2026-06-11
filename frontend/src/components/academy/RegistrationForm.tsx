"use client";

import Link from "next/link";
import { FormEvent, useEffect, useMemo, useState } from "react";
import { CountrySelect } from "@/components/form/CountrySelect";
import { DEFAULT_COUNTRY } from "@/data/countries";
import {
  buildParticipantUrl,
  checkAcademyPaymentStatus,
  processAcademyPayment,
  submitRegistration,
} from "@/lib/api";
import { validateMobileMoneyPhone } from "@/lib/mobile-money";
import {
  checkPaymentOnce,
  MAX_MANUAL_VERIFY_ATTEMPTS,
  pollPaymentAuto,
} from "@/lib/payment-polling";
import { SessionRegistrationBenefits } from "@/components/academy/SessionRegistrationBenefits";
import { useRegistrationBenefits } from "@/hooks/useRegistrationBenefits";
import { REGISTRATION_STATUS_STYLES } from "@/lib/registration-status";
import type {
  MobileMoneyOperator,
  PaymentChannel,
  PaymentCurrency,
  PaymentCurrencyOption,
  RegistrationPayload,
  SessionPaymentInfo,
  TrainingSession,
} from "@/types/api";

interface RegistrationFormProps {
  session: TrainingSession;
  /** hero : champs et carte plus grands pour la page session. */
  variant?: "default" | "hero";
}

type WizardStep = "personal" | "profile" | "summary" | "payment" | "done";

const ALL_MOBILE_OPERATORS: MobileMoneyOperator[] = [
  "mpesa",
  "airtel",
  "orange",
  "afrimoney",
];

/**
 * Retourne les opérateurs Mobile Money activés pour la session.
 */
function enabledMobileOperators(session: TrainingSession): MobileMoneyOperator[] {
  if (
    session.enabled_mobile_operators &&
    session.enabled_mobile_operators.length > 0
  ) {
    return session.enabled_mobile_operators.filter((operator): operator is MobileMoneyOperator =>
      ALL_MOBILE_OPERATORS.includes(operator)
    );
  }

  if (session.payment_mobile_money_enabled === false) {
    return [];
  }

  return ALL_MOBILE_OPERATORS;
}

/**
 * Retourne les canaux de paiement activés (Mobile Money si au moins un opérateur).
 */
function enabledPaymentChannels(session: TrainingSession): PaymentChannel[] {
  const channels: PaymentChannel[] = [];

  if (enabledMobileOperators(session).length > 0) {
    channels.push("mobile_money");
  }

  if (session.payment_card_enabled !== false) {
    channels.push("card");
  }

  return channels;
}

interface FormState {
  firstname: string;
  lastname: string;
  email: string;
  phone: string;
  city: string;
  country: string;
  educationLevel: string;
  motivation: string;
  marketingOptIn: boolean;
  notifyEmail: boolean;
  notifySms: boolean;
  notifyWhatsapp: boolean;
}

const MOBILE_OPERATORS: { id: MobileMoneyOperator; label: string; hint: string }[] = [
  { id: "mpesa", label: "M-Pesa", hint: "Vodacom" },
  { id: "airtel", label: "Airtel Money", hint: "Airtel" },
  { id: "orange", label: "Orange Money", hint: "Orange" },
  { id: "afrimoney", label: "Afrimoney", hint: "Africell" },
];

const STEP_LABELS: Record<WizardStep, string> = {
  personal: "Identité",
  profile: "Profil",
  summary: "Récapitulatif",
  payment: "Paiement",
  done: "Terminé",
};

const inputClassDefault =
  "w-full rounded-xl border border-line bg-bg-elev px-4 py-3.5 text-ink outline-none transition-colors focus:border-accent";

const inputClassHero =
  "w-full rounded-xl border border-line bg-bg-elev px-5 py-4 text-base text-ink outline-none transition-colors focus:border-accent md:text-lg";

/**
 * Affiche le montant en double devise si disponible.
 */
function formatSessionPrice(session: TrainingSession): string | null {
  if (!session.is_paid) {
    return null;
  }

  if (session.formatted_price) {
    return session.formatted_price;
  }

  if (session.price_usd != null && session.price_cdf != null) {
    return `${session.price_usd.toLocaleString("fr-FR")} USD (${session.price_cdf.toLocaleString("fr-FR")} CDF)`;
  }

  if (session.price != null) {
    return `${session.price} ${session.currency ?? "USD"}`;
  }

  return null;
}

/**
 * Options de devise disponibles à l'étape paiement (équivalent du tarif session).
 */
function resolveCurrencyOptions(
  payment: SessionPaymentInfo,
  session: TrainingSession
): PaymentCurrencyOption[] {
  if (payment.currency_options && payment.currency_options.length > 0) {
    return payment.currency_options;
  }

  const options: PaymentCurrencyOption[] = [];

  if (session.price_usd != null) {
    options.push({
      currency: "USD",
      amount: session.price_usd,
      formatted: formatCurrencyAmount(session.price_usd, "USD"),
    });
  }

  if (session.price_cdf != null) {
    options.push({
      currency: "CDF",
      amount: session.price_cdf,
      formatted: formatCurrencyAmount(session.price_cdf, "CDF"),
    });
  }

  if (options.length === 0) {
    const currency = (payment.currency || session.currency || "USD") as PaymentCurrency;

    options.push({
      currency,
      amount: payment.amount,
      formatted: formatCurrencyAmount(payment.amount, currency),
    });
  }

  return options;
}

/**
 * Formate un montant selon la devise.
 */
function formatCurrencyAmount(amount: number, currency: string): string {
  if (currency === "CDF") {
    return `${amount.toLocaleString("fr-FR", { maximumFractionDigits: 0 })} CDF`;
  }

  return `${amount.toLocaleString("fr-FR", {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  })} ${currency}`;
}

/**
 * Montant affiché pour la devise sélectionnée à l'étape paiement.
 */
function formatSelectedPaymentTotal(option: PaymentCurrencyOption | undefined): string {
  if (!option) {
    return "—";
  }

  return option.formatted ?? formatCurrencyAmount(option.amount, option.currency);
}

/**
 * Formulaire d'inscription multi-étapes avec paiement si session payante.
 */
export function RegistrationForm({
  session,
  variant = "default",
}: RegistrationFormProps) {
  const isHero = variant === "hero";
  const inputClass = isHero ? inputClassHero : inputClassDefault;
  const cardClass = isHero
    ? "card-lg p-8 shadow-[0_20px_60px_rgba(0,31,63,0.08)] md:p-10 lg:p-12"
    : "card-lg p-6 md:p-8";
  const titleClass = isHero
    ? "font-display text-3xl tracking-tight md:text-4xl"
    : "font-display text-2xl tracking-tight";
  const isPaidSession = session.is_paid === true && !session.is_free;
  const availablePaymentChannels = useMemo(
    () => enabledPaymentChannels(session),
    [
      session.payment_mobile_money_enabled,
      session.payment_card_enabled,
      session.enabled_mobile_operators,
    ]
  );
  const availableMobileOperators = useMemo(
    () =>
      MOBILE_OPERATORS.filter((operator) =>
        enabledMobileOperators(session).includes(operator.id)
      ),
    [session.enabled_mobile_operators, session.payment_mobile_money_enabled]
  );
  const showPaymentMethodChoice = availablePaymentChannels.length > 1;
  const showMobileOperatorChoice = availableMobileOperators.length > 1;
  const currencyOptions = useMemo(
    () => (paymentInfo ? resolveCurrencyOptions(paymentInfo, session) : []),
    [paymentInfo, session]
  );
  const showCurrencyChoice = currencyOptions.length > 1;
  const selectedCurrencyOption = currencyOptions.find(
    (option) => option.currency === paymentCurrency
  );
  const benefits = useRegistrationBenefits(session.slug, session);
  const stepOrder: WizardStep[] = isPaidSession
    ? ["personal", "profile", "summary", "payment", "done"]
    : ["personal", "profile", "summary", "done"];

  const [step, setStep] = useState<WizardStep>("personal");
  const [loading, setLoading] = useState(false);
  const [polling, setPolling] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const [paymentInfo, setPaymentInfo] = useState<SessionPaymentInfo | null>(null);
  const [paymentCurrency, setPaymentCurrency] = useState<PaymentCurrency | "">("");
  const [channel, setChannel] = useState<PaymentChannel | "">("");
  const [mobileOperator, setMobileOperator] = useState<MobileMoneyOperator | "">("");
  const [phone, setPhone] = useState("");
  const [termsAccepted, setTermsAccepted] = useState(false);
  const [showManualVerify, setShowManualVerify] = useState(false);
  const [verifyAttempts, setVerifyAttempts] = useState(0);
  const [participantToken, setParticipantToken] = useState<string | null>(null);
  const [resumeInfo, setResumeInfo] = useState<string | null>(null);

  useEffect(() => {
    if (step !== "payment" || availablePaymentChannels.length !== 1) {
      return;
    }

    const onlyChannel = availablePaymentChannels[0];

    if (channel !== onlyChannel) {
      setChannel(onlyChannel);

      if (onlyChannel === "card") {
        setMobileOperator("");
      }
    }
  }, [step, availablePaymentChannels, channel]);

  useEffect(() => {
    if (step !== "payment" || currencyOptions.length === 0) {
      return;
    }

    const defaultCurrency = currencyOptions[0].currency;

    if (
      paymentCurrency === "" ||
      !currencyOptions.some((option) => option.currency === paymentCurrency)
    ) {
      setPaymentCurrency(defaultCurrency);
    }
  }, [step, currencyOptions, paymentCurrency]);

  useEffect(() => {
    if (step !== "payment" || channel !== "mobile_money") {
      return;
    }

    if (availableMobileOperators.length !== 1) {
      return;
    }

    const onlyOperator = availableMobileOperators[0].id;

    if (mobileOperator !== onlyOperator) {
      setMobileOperator(onlyOperator);
    }
  }, [step, channel, availableMobileOperators, mobileOperator]);

  const initialFormState: FormState = {
    firstname: "",
    lastname: "",
    email: "",
    phone: "",
    city: "",
    country: DEFAULT_COUNTRY,
    educationLevel: "",
    motivation: "",
    marketingOptIn: true,
    notifyEmail: session.notify_by_email !== false,
    notifySms: false,
    notifyWhatsapp: false,
  };

  const [form, setForm] = useState<FormState>(initialFormState);

  const priceLabel = formatSessionPrice(session);

  /**
   * Met à jour un champ du formulaire.
   */
  function updateField<K extends keyof FormState>(key: K, value: FormState[K]) {
    setForm((prev) => ({ ...prev, [key]: value }));
  }

  const stepIndex = stepOrder.indexOf(step);

  /**
   * Réinitialise le formulaire pour une nouvelle inscription.
   */
  function resetToStart() {
    setStep("personal");
    setForm(initialFormState);
    setPaymentInfo(null);
    setChannel("");
    setMobileOperator("");
    setPhone("");
    setTermsAccepted(false);
    setShowManualVerify(false);
    setVerifyAttempts(0);
    setParticipantToken(null);
    setResumeInfo(null);
    setError(null);
    setLoading(false);
    setPolling(false);
  }

  /**
   * Passe à l'étape suivante avec validation minimale.
   */
  function goNext() {
    setError(null);

    if (step === "personal") {
      if (!form.firstname.trim() || !form.lastname.trim() || !form.email.trim()) {
        setError("Prénom, nom et e-mail sont obligatoires.");
        return;
      }
      setStep("profile");
      return;
    }

    if (step === "profile") {
      setStep("summary");
    }
  }

  /**
   * Retour à l'étape précédente.
   */
  function goBack() {
    setError(null);
    if (step === "profile") {
      setStep("personal");
    } else if (step === "summary") {
      setStep("profile");
    } else if (step === "payment") {
      setStep("summary");
    }
  }

  /**
   * Soumet l'inscription à l'API.
   */
  async function handleRegister() {
    setLoading(true);
    setError(null);

    const payload: RegistrationPayload = {
      training_session_slug: session.slug,
      firstname: form.firstname.trim(),
      lastname: form.lastname.trim(),
      email: form.email.trim(),
      phone: form.phone.trim() || undefined,
      city: form.city.trim() || undefined,
      country: form.country.trim() || DEFAULT_COUNTRY,
      education_level: form.educationLevel.trim() || undefined,
      motivation: form.motivation.trim() || undefined,
      marketing_opt_in: form.marketingOptIn,
      notify_email: form.notifyEmail,
      notify_sms: form.notifySms,
      notify_whatsapp: form.notifyWhatsapp,
    };

    try {
      const result = await submitRegistration(payload);

      if (!result.success || !result.data) {
        setError(
          result.message ||
            "Inscription impossible. Vérifiez vos informations ou réessayez."
        );
        return;
      }

      const token =
        result.data.access_token ?? result.data.registration?.access_token ?? null;

      if (token) {
        setParticipantToken(token);
      }

      if (result.message) {
        setResumeInfo(result.message);
      }

      if (
        result.data.resume_action === "payment" &&
        result.data.requires_payment &&
        result.data.payment
      ) {
        setPaymentInfo(result.data.payment);
        setVerifyAttempts(0);
        setShowManualVerify(false);
        setStep("payment");
        return;
      }

      if (
        result.data.resume_action === "participant_space" ||
        result.data.is_paid ||
        !result.data.requires_payment
      ) {
        setStep("done");
        return;
      }

      if (result.data.requires_payment && result.data.payment) {
        setPaymentInfo(result.data.payment);
        setStep("payment");
      } else {
        setStep("done");
      }
    } catch {
      setError("Inscription impossible pour le moment. Réessayez plus tard.");
    } finally {
      setLoading(false);
    }
  }

  /**
   * Confirme le paiement après polling ou relance manuelle.
   */
  async function runPaymentConfirmation(reference: string): Promise<boolean> {
    const result = await checkPaymentOnce(reference);

    if (result.confirmed) {
      setStep("done");
      setShowManualVerify(false);
      setError(null);
      return true;
    }

    if (result.cancelled) {
      setError(result.message || "Paiement annulé.");
      return false;
    }

    return false;
  }

  /**
   * Relance manuelle (max 3 tentatives) si débit sans confirmation auto.
   */
  async function handleManualVerify() {
    if (!paymentInfo?.reference) {
      return;
    }

    if (verifyAttempts >= MAX_MANUAL_VERIFY_ATTEMPTS) {
      setError("Nombre maximum de vérifications atteint. Contactez le support avec votre référence.");
      return;
    }

    setLoading(true);
    setError(null);
    setVerifyAttempts((n) => n + 1);

    try {
      const res = await checkAcademyPaymentStatus(paymentInfo.reference);
      const data = res.data;

      if (data?.confirmed || (data?.reponse && data.status === 0)) {
        setStep("done");
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

  /**
   * Lance le paiement (mobile ou carte).
   */
  async function handlePayment(event: FormEvent) {
    event.preventDefault();
    setError(null);
    setShowManualVerify(false);
    setVerifyAttempts(0);

    if (!paymentInfo?.reference) {
      setError("Référence de paiement manquante. Recommencez l'inscription.");
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

    if (!paymentCurrency || !currencyOptions.some((option) => option.currency === paymentCurrency)) {
      setError("Choisissez la devise de paiement.");
      return;
    }

    if (channel === "mobile_money") {
      if (availableMobileOperators.length === 0) {
        setError("Aucun opérateur Mobile Money n'est disponible pour cette session.");
        return;
      }

      if (!mobileOperator || !enabledMobileOperators(session).includes(mobileOperator)) {
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
      const res = await processAcademyPayment({
        reference: paymentInfo.reference,
        channel,
        payment_currency: paymentCurrency,
        phone: channel === "mobile_money" ? phone.trim() : undefined,
        mobile_operator:
          channel === "mobile_money" && mobileOperator
            ? mobileOperator
            : undefined,
      });

      if (res.data?.type === "already_paid" || (res.success && res.data?.reponse && res.data?.status === 0)) {
        setStep("done");
        return;
      }

      if (!res.success || !res.data?.reponse) {
        setError(res.message || res.data?.message || "Échec du paiement.");
        return;
      }

      if (res.data.type === "card" && res.data.redirect_url) {
        window.location.href = res.data.redirect_url;
        return;
      }

      if (res.data.type === "mobile") {
        setPolling(true);
        const pollResult = await pollPaymentAuto(paymentInfo.reference);
        setPolling(false);

        if (pollResult.confirmed) {
          setStep("done");
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

  if (!session.accepts_registrations) {
    return (
      <div className="card-lg p-8 text-center text-muted">
        Les inscriptions pour cette session sont fermées.
      </div>
    );
  }

  return (
    <div className={cardClass}>
      <div className={`flex flex-wrap items-center justify-between gap-3 ${isHero ? "mb-8" : "mb-6"}`}>
        <h2 className={titleClass}>Inscription à la formation</h2>
        {isPaidSession && priceLabel && (
          <span className="rounded-full border border-academy/30 bg-academy-soft px-4 py-1.5 text-sm font-semibold text-academy">
            {priceLabel}
          </span>
        )}
        {!isPaidSession && (
          <span className="rounded-full border border-line bg-bg px-4 py-1.5 text-sm text-ink-soft">
            Gratuit
          </span>
        )}
      </div>

      <nav className="mb-8 flex flex-wrap gap-2" aria-label="Étapes">
        {stepOrder
          .filter((s) => s !== "done")
          .map((s, index) => {
            const active = stepIndex === index;
            const done = stepIndex > index;

            return (
              <span
                key={s}
                className={`rounded-full px-3 py-1 text-xs font-medium ${
                  active
                    ? "bg-accent-soft text-accent"
                    : done
                      ? "bg-bg text-ink-soft"
                      : "bg-bg-elev text-muted"
                }`}
              >
                {index + 1}. {STEP_LABELS[s]}
              </span>
            );
          })}
      </nav>

      {error && (
        <p className={`mb-4 rounded-xl border px-4 py-3 text-sm ${REGISTRATION_STATUS_STYLES.danger}`} role="alert">
          {error}
        </p>
      )}

      {step === "personal" && (
        <div className="space-y-4">
          <div className="grid gap-4 md:grid-cols-2">
            <input
              required
              placeholder="Prénom *"
              className={inputClass}
              value={form.firstname}
              onChange={(e) => updateField("firstname", e.target.value)}
            />
            <input
              required
              placeholder="Nom *"
              className={inputClass}
              value={form.lastname}
              onChange={(e) => updateField("lastname", e.target.value)}
            />
            <input
              required
              type="email"
              placeholder="E-mail *"
              className={inputClass}
              value={form.email}
              onChange={(e) => updateField("email", e.target.value)}
            />
            <input
              placeholder="Téléphone"
              className={inputClass}
              value={form.phone}
              onChange={(e) => updateField("phone", e.target.value)}
            />
            <input
              placeholder="Ville"
              className={inputClass}
              value={form.city}
              onChange={(e) => updateField("city", e.target.value)}
            />
            <CountrySelect
              value={form.country}
              onChange={(v) => updateField("country", v)}
            />
          </div>
          <div className="flex justify-end gap-3">
            <button type="button" className="btn btn-gold" onClick={goNext}>
              Continuer
            </button>
          </div>
        </div>
      )}

      {step === "profile" && (
        <div className="space-y-4">
          <input
            placeholder="Niveau d'études"
            className={inputClass}
            value={form.educationLevel}
            onChange={(e) => updateField("educationLevel", e.target.value)}
          />
          <textarea
            rows={4}
            placeholder="Motivation (optionnel)"
            className={inputClass}
            value={form.motivation}
            onChange={(e) => updateField("motivation", e.target.value)}
          />
          <label className="flex items-start gap-3 text-sm text-ink-soft">
            <input
              type="checkbox"
              checked={form.marketingOptIn}
              onChange={(e) => updateField("marketingOptIn", e.target.checked)}
              className="mt-1"
            />
            J&apos;accepte de recevoir les communications de SDev Academy.
          </label>

          <div className="rounded-2xl border border-line bg-bg p-4 md:p-5">
            <p className="mb-3 text-sm font-medium text-ink">
              Me notifier et me rappeler
            </p>
            <div className="space-y-2 text-sm text-ink-soft">
              {session.notify_by_email !== false && (
                <label className="flex items-center gap-2">
                  <input
                    type="checkbox"
                    checked={form.notifyEmail}
                    onChange={(e) => updateField("notifyEmail", e.target.checked)}
                  />
                  Par e-mail (confirmation + rappel)
                </label>
              )}
              {session.notify_by_sms && (
                <label className="flex items-center gap-2">
                  <input
                    type="checkbox"
                    checked={form.notifySms}
                    onChange={(e) => updateField("notifySms", e.target.checked)}
                  />
                  Par SMS
                </label>
              )}
              {session.notify_by_whatsapp && (
                <label className="flex items-center gap-2">
                  <input
                    type="checkbox"
                    checked={form.notifyWhatsapp}
                    onChange={(e) => updateField("notifyWhatsapp", e.target.checked)}
                  />
                  Par WhatsApp
                </label>
              )}
            </div>
          </div>

          <div className="flex justify-between gap-3">
            <button type="button" className="btn btn-outline" onClick={goBack}>
              Retour
            </button>
            <button type="button" className="btn btn-gold" onClick={goNext}>
              Continuer
            </button>
          </div>
        </div>
      )}

      {step === "summary" && (
        <div className="space-y-4">
          <div className="rounded-2xl border border-line bg-bg p-4 text-sm text-ink-soft md:p-5">
            <p>
              <strong className="text-ink">
                {form.firstname} {form.lastname}
              </strong>
            </p>
            <p>{form.email}</p>
            {form.phone && <p>{form.phone}</p>}
            <p>{form.country}</p>
            <p className="mt-3 text-academy">{session.title}</p>
            {isPaidSession && priceLabel && (
              <p className="mt-2 font-semibold text-academy">
                Montant à payer : {priceLabel}
              </p>
            )}
          </div>
          <div className="flex justify-between gap-3">
            <button type="button" className="btn btn-outline" onClick={goBack}>
              Retour
            </button>
            <button
              type="button"
              className="btn btn-gold btn-lg"
              disabled={loading}
              onClick={handleRegister}
            >
              {loading
                ? "Enregistrement..."
                : isPaidSession
                  ? "Confirmer et payer"
                  : "Confirmer mon inscription"}
            </button>
          </div>
        </div>
      )}

      {step === "payment" && paymentInfo && (
        <form onSubmit={handlePayment} className="space-y-5">
          {resumeInfo && (
            <p className={`rounded-2xl border px-4 py-3 text-sm ${REGISTRATION_STATUS_STYLES.info}`}>
              {resumeInfo}
            </p>
          )}
          <div className="space-y-3">
            {showCurrencyChoice ? (
              <div>
                <p className="mb-2 text-sm font-medium text-ink">
                  Devise de paiement *
                </p>
                <p className="mb-3 text-xs text-muted">
                  Même tarif d&apos;inscription — choisissez la devise disponible
                  sur votre compte (le montant de la session ne change pas).
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
                      <span className="font-semibold text-ink">
                        {option.currency}
                      </span>
                      <span className="mt-1 block text-sm text-academy">
                        {formatSelectedPaymentTotal(option)}
                      </span>
                    </button>
                  ))}
                </div>
              </div>
            ) : (
              <p className="text-sm text-ink-soft">
                Total :{" "}
                <strong className="text-academy">
                  {formatSelectedPaymentTotal(selectedCurrencyOption ?? currencyOptions[0])}
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
              Le paiement en ligne n&apos;est pas configuré pour cette session.
              Contactez l&apos;équipe SDev Academy.
            </p>
          ) : showPaymentMethodChoice ? (
            <div>
              <p className="mb-2 text-sm font-medium text-ink">
                Moyen de paiement *
              </p>
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
                    <span className="mt-1 block text-xs text-muted">
                      Visa, Mastercard
                    </span>
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
                  Aucun opérateur Mobile Money n&apos;est configuré pour cette
                  session.
                </p>
              ) : showMobileOperatorChoice ? (
                <div>
                  <p className="mb-2 text-sm font-medium text-ink">
                    Opérateur *
                  </p>
                  <div
                    className={`grid gap-2 ${
                      availableMobileOperators.length >= 4
                        ? "grid-cols-2 sm:grid-cols-4"
                        : availableMobileOperators.length === 3
                          ? "grid-cols-2 sm:grid-cols-3"
                          : "grid-cols-2"
                    }`}
                  >
                    {availableMobileOperators.map((op) => (
                      <button
                        key={op.id}
                        type="button"
                        className={`rounded-xl border px-3 py-3 text-center text-sm transition ${
                          mobileOperator === op.id
                            ? "border-academy/50 bg-academy-soft text-academy"
                            : "border-line text-ink-soft hover:border-academy/30"
                        }`}
                        onClick={() => setMobileOperator(op.id)}
                      >
                        <span className="block font-medium">{op.label}</span>
                        <span className="text-xs opacity-70">{op.hint}</span>
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
                    onChange={(e) => setPhone(e.target.value)}
                  />
                  <p className="text-xs text-muted">
                    Le numéro doit commencer par 243 et correspondre à
                    l&apos;opérateur choisi.
                  </p>
                </>
              )}
            </>
          )}

          <label className="flex items-start gap-3 text-sm text-ink-soft">
            <input
              type="checkbox"
              checked={termsAccepted}
              onChange={(e) => setTermsAccepted(e.target.checked)}
              className="mt-1"
            />
            J&apos;accepte les conditions générales et la politique de paiement.
          </label>

          {polling && (
            <p className={`rounded-xl border px-4 py-3 text-sm ${REGISTRATION_STATUS_STYLES.warning}`}>
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

          <div className="flex justify-between gap-3">
            <button
              type="button"
              className="btn btn-outline"
              disabled={loading || polling}
              onClick={goBack}
            >
              Retour
            </button>
            <button
              type="submit"
              className="btn btn-gold btn-lg"
              disabled={
                loading || polling || availablePaymentChannels.length === 0
              }
            >
              {loading || polling ? "Traitement..." : "Payer maintenant"}
            </button>
          </div>
        </form>
      )}

      {step === "done" && (
        <div className="space-y-6 text-center">
          <p className={`text-lg font-semibold ${REGISTRATION_STATUS_STYLES.success}`}>
            {resumeInfo?.includes("déjà inscrit")
              ? "Bienvenue à nouveau !"
              : "Inscription confirmée !"}
          </p>
          <p className="text-sm text-ink-soft">
            {resumeInfo?.includes("déjà inscrit") ? (
              <>
                Vous êtes déjà inscrit(e) à cette session avec{" "}
                <strong className="text-ink">{form.email}</strong>. Accédez à votre
                espace pour le compte à rebours et vos ressources.
              </>
            ) : (
              <>
                Merci {form.firstname}. Un e-mail de confirmation a été envoyé à{" "}
                <strong className="text-ink">{form.email}</strong> avec le lien vers
                votre espace formation.
              </>
            )}
          </p>

          {participantToken && (
            <Link
              href={buildParticipantUrl(participantToken)}
              className="btn btn-gold btn-lg inline-flex"
            >
              Accéder à mon espace formation
            </Link>
          )}

          <div className="flex flex-col items-center gap-3 sm:flex-row sm:justify-center">
            <button type="button" className="btn btn-outline" onClick={resetToStart}>
              Nouvelle inscription
            </button>
            <Link href="/" className="btn btn-outline">
              Retour à l&apos;accueil
            </Link>
          </div>
        </div>
      )}

      {step !== "done" && (
        <SessionRegistrationBenefits benefits={benefits} variant="inline" />
      )}
    </div>
  );
}
