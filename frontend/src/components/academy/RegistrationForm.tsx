"use client";

import Link from "next/link";
import { FormEvent, useState } from "react";
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
import type {
  MobileMoneyOperator,
  PaymentChannel,
  RegistrationPayload,
  SessionPaymentInfo,
  TrainingSession,
} from "@/types/api";

interface RegistrationFormProps {
  session: TrainingSession;
}

type WizardStep = "personal" | "profile" | "summary" | "payment" | "done";

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

const inputClass =
  "w-full rounded-2xl border border-amber-500/15 bg-white/[0.03] px-4 py-3.5 text-white outline-none focus:border-amber-500/45";

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
 * Montant affiché à l'étape paiement.
 */
function formatPaymentTotal(
  payment: SessionPaymentInfo,
  session: TrainingSession
): string {
  const dual = formatSessionPrice(session);

  if (dual) {
    return dual;
  }

  return `${payment.amount} ${payment.currency}`;
}

/**
 * Formulaire d'inscription multi-étapes avec paiement si session payante.
 */
export function RegistrationForm({ session }: RegistrationFormProps) {
  const isPaidSession = session.is_paid === true && !session.is_free;
  const stepOrder: WizardStep[] = isPaidSession
    ? ["personal", "profile", "summary", "payment", "done"]
    : ["personal", "profile", "summary", "done"];

  const [step, setStep] = useState<WizardStep>("personal");
  const [loading, setLoading] = useState(false);
  const [polling, setPolling] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const [paymentInfo, setPaymentInfo] = useState<SessionPaymentInfo | null>(null);
  const [channel, setChannel] = useState<PaymentChannel | "">("");
  const [mobileOperator, setMobileOperator] = useState<MobileMoneyOperator | "">("");
  const [phone, setPhone] = useState("");
  const [termsAccepted, setTermsAccepted] = useState(false);
  const [showManualVerify, setShowManualVerify] = useState(false);
  const [verifyAttempts, setVerifyAttempts] = useState(0);
  const [participantToken, setParticipantToken] = useState<string | null>(null);

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
        setError(result.message || "Inscription impossible.");
        return;
      }

      const token =
        result.data.access_token ?? result.data.registration?.access_token ?? null;

      if (token) {
        setParticipantToken(token);
      }

      if (result.data.requires_payment && result.data.payment) {
        setPaymentInfo(result.data.payment);
        setVerifyAttempts(0);
        setShowManualVerify(false);
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

    if (!channel) {
      setError("Choisissez un moyen de paiement.");
      return;
    }

    if (channel === "mobile_money") {
      if (!mobileOperator) {
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
      <div className="glass rounded-3xl p-8 text-center text-slate-300">
        Les inscriptions pour cette session sont fermées.
      </div>
    );
  }

  return (
    <div className="glass rounded-3xl p-6 md:p-8">
      <div className="mb-6 flex flex-wrap items-center justify-between gap-3">
        <h2 className="text-2xl font-bold">Inscription</h2>
        {isPaidSession && priceLabel && (
          <span className="rounded-full border border-amber-500/40 bg-amber-500/10 px-4 py-1.5 text-sm font-semibold text-amber-300">
            {priceLabel}
          </span>
        )}
        {!isPaidSession && (
          <span className="rounded-full border border-green-500/30 bg-green-500/10 px-4 py-1.5 text-sm text-green-400">
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
                    ? "bg-amber-500/25 text-amber-200"
                    : done
                      ? "bg-white/10 text-slate-300"
                      : "bg-white/5 text-slate-500"
                }`}
              >
                {index + 1}. {STEP_LABELS[s]}
              </span>
            );
          })}
      </nav>

      {error && (
        <p className="mb-4 text-sm text-red-400" role="alert">
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
          <label className="flex items-start gap-3 text-sm text-slate-300">
            <input
              type="checkbox"
              checked={form.marketingOptIn}
              onChange={(e) => updateField("marketingOptIn", e.target.checked)}
              className="mt-1"
            />
            J&apos;accepte de recevoir les communications de SDev Academy.
          </label>

          <div className="rounded-2xl border border-white/10 bg-white/[0.02] p-4">
            <p className="mb-3 text-sm font-medium text-slate-200">
              Me notifier et me rappeler
            </p>
            <div className="space-y-2 text-sm text-slate-300">
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
          <div className="rounded-2xl border border-white/10 bg-white/[0.02] p-4 text-sm text-slate-300">
            <p>
              <strong className="text-white">
                {form.firstname} {form.lastname}
              </strong>
            </p>
            <p>{form.email}</p>
            {form.phone && <p>{form.phone}</p>}
            <p>{form.country}</p>
            <p className="mt-3 text-amber-200/90">{session.title}</p>
            {isPaidSession && priceLabel && (
              <p className="mt-2 font-semibold text-amber-300">
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
          <p className="text-sm text-slate-300">
            Total :{" "}
            <strong className="text-amber-300">
              {formatPaymentTotal(paymentInfo, session)}
            </strong>
            <span className="ml-2 block text-xs text-slate-500 sm:inline">
              Réf. {paymentInfo.reference}
            </span>
          </p>

          <div>
            <p className="mb-2 text-sm font-medium text-slate-200">
              Moyen de paiement *
            </p>
            <div className="grid gap-3 sm:grid-cols-2">
              <button
                type="button"
                className={`rounded-2xl border p-4 text-left transition ${
                  channel === "mobile_money"
                    ? "border-amber-500/50 bg-amber-500/10"
                    : "border-white/10 hover:border-amber-500/25"
                }`}
                onClick={() => setChannel("mobile_money")}
              >
                <span className="font-semibold text-white">Mobile Money</span>
                <span className="mt-1 block text-xs text-slate-400">
                  M-Pesa, Airtel, Orange, Afrimoney
                </span>
              </button>
              <button
                type="button"
                className={`rounded-2xl border p-4 text-left transition ${
                  channel === "card"
                    ? "border-amber-500/50 bg-amber-500/10"
                    : "border-white/10 hover:border-amber-500/25"
                }`}
                onClick={() => {
                  setChannel("card");
                  setMobileOperator("");
                }}
              >
                <span className="font-semibold text-white">Carte bancaire</span>
                <span className="mt-1 block text-xs text-slate-400">
                  Visa, Mastercard
                </span>
              </button>
            </div>
          </div>

          {channel === "mobile_money" && (
            <>
              <div>
                <p className="mb-2 text-sm font-medium text-slate-200">
                  Opérateur *
                </p>
                <div className="grid grid-cols-2 gap-2 sm:grid-cols-4">
                  {MOBILE_OPERATORS.map((op) => (
                    <button
                      key={op.id}
                      type="button"
                      className={`rounded-xl border px-3 py-3 text-center text-sm transition ${
                        mobileOperator === op.id
                          ? "border-amber-500/50 bg-amber-500/15 text-amber-100"
                          : "border-white/10 text-slate-300 hover:border-amber-500/30"
                      }`}
                      onClick={() => setMobileOperator(op.id)}
                    >
                      <span className="block font-medium">{op.label}</span>
                      <span className="text-xs opacity-70">{op.hint}</span>
                    </button>
                  ))}
                </div>
              </div>
              <input
                required
                placeholder="Téléphone Mobile Money (243…) *"
                className={inputClass}
                value={phone}
                onChange={(e) => setPhone(e.target.value)}
              />
              <p className="text-xs text-slate-500">
                Le numéro doit commencer par 243 et correspondre à l&apos;opérateur choisi.
              </p>
            </>
          )}

          <label className="flex items-start gap-3 text-sm text-slate-300">
            <input
              type="checkbox"
              checked={termsAccepted}
              onChange={(e) => setTermsAccepted(e.target.checked)}
              className="mt-1"
            />
            J&apos;accepte les conditions générales et la politique de paiement.
          </label>

          {polling && (
            <p className="text-sm text-amber-300">
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
              disabled={loading || polling}
            >
              {loading || polling ? "Traitement..." : "Payer maintenant"}
            </button>
          </div>
        </form>
      )}

      {step === "done" && (
        <div className="space-y-6 text-center">
          <p className="text-lg font-semibold text-green-400">
            Inscription confirmée !
          </p>
          <p className="text-sm text-slate-300">
            Merci {form.firstname}. Un e-mail de confirmation a été envoyé à{" "}
            <strong className="text-white">{form.email}</strong> avec le lien vers
            votre espace formation.
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
    </div>
  );
}
