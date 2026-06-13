"use client";

import { FormEvent, useState } from "react";
import { submitPreRegistration } from "@/lib/api";
import { getRegistrationStatusStyle } from "@/lib/registration-status";
import type { TrainingSession } from "@/types/api";

interface PreRegistrationFormProps {
  session: TrainingSession;
}

/**
 * Formulaire léger de pré-inscription (intérêt avant ouverture officielle).
 */
export function PreRegistrationForm({ session }: PreRegistrationFormProps) {
  const [firstname, setFirstname] = useState("");
  const [lastname, setLastname] = useState("");
  const [email, setEmail] = useState("");
  const [phone, setPhone] = useState("");
  const [marketingOptIn, setMarketingOptIn] = useState(true);
  const [loading, setLoading] = useState(false);
  const [message, setMessage] = useState<string | null>(null);
  const [isSuccess, setIsSuccess] = useState(false);

  const inputClass =
    "w-full rounded-xl border border-line bg-bg-elev px-4 py-3.5 text-ink outline-none transition-colors focus:border-accent";

  /**
   * Envoie la pré-inscription à l'API.
   */
  async function handleSubmit(event: FormEvent<HTMLFormElement>) {
    event.preventDefault();
    setLoading(true);
    setMessage(null);

    const result = await submitPreRegistration({
      training_session_slug: session.slug,
      firstname: firstname.trim(),
      lastname: lastname.trim(),
      email: email.trim(),
      phone: phone.trim() || undefined,
      marketing_opt_in: marketingOptIn,
    });

    setLoading(false);

    if (result.success) {
      setIsSuccess(true);
      setMessage(result.message);
      return;
    }

    setIsSuccess(false);
    setMessage(result.message || "Une erreur est survenue.");
  }

  if (isSuccess) {
    return (
      <div className={`card-lg rounded-2xl p-6 md:p-8 ${getRegistrationStatusStyle("confirmed")}`}>
        <p className="text-lg font-semibold">Merci pour votre intérêt</p>
        <p className="mt-2 text-sm leading-relaxed opacity-90">{message}</p>
      </div>
    );
  }

  return (
    <form onSubmit={handleSubmit} className="card-lg space-y-4 rounded-2xl p-6 md:p-8">
      <div>
        <h2 className="font-display text-2xl tracking-tight">Pré-inscription</h2>
        <p className="mt-2 text-sm text-muted">
          Laissez vos coordonnées pour être prévenu(e) dès l&apos;ouverture des inscriptions.
        </p>
      </div>

      {message && !isSuccess && (
        <p className={`rounded-xl px-4 py-3 text-sm ${getRegistrationStatusStyle("cancelled")}`}>
          {message}
        </p>
      )}

      <div className="grid gap-4 sm:grid-cols-2">
        <label className="block">
          <span className="mb-1.5 block text-sm font-medium">Prénom</span>
          <input
            type="text"
            required
            value={firstname}
            onChange={(event) => setFirstname(event.target.value)}
            className={inputClass}
            autoComplete="given-name"
          />
        </label>
        <label className="block">
          <span className="mb-1.5 block text-sm font-medium">Nom</span>
          <input
            type="text"
            required
            value={lastname}
            onChange={(event) => setLastname(event.target.value)}
            className={inputClass}
            autoComplete="family-name"
          />
        </label>
      </div>

      <label className="block">
        <span className="mb-1.5 block text-sm font-medium">E-mail</span>
        <input
          type="email"
          required
          value={email}
          onChange={(event) => setEmail(event.target.value)}
          className={inputClass}
          autoComplete="email"
        />
      </label>

      <label className="block">
        <span className="mb-1.5 block text-sm font-medium">Téléphone (optionnel)</span>
        <input
          type="tel"
          value={phone}
          onChange={(event) => setPhone(event.target.value)}
          className={inputClass}
          autoComplete="tel"
        />
      </label>

      <label className="flex items-start gap-3 text-sm text-muted">
        <input
          type="checkbox"
          checked={marketingOptIn}
          onChange={(event) => setMarketingOptIn(event.target.checked)}
          className="mt-1 h-4 w-4 rounded border-line accent-accent"
        />
        <span>J&apos;accepte de recevoir des informations sur cette formation et les prochaines sessions SDev Academy.</span>
      </label>

      <button
        type="submit"
        disabled={loading}
        className="btn btn-gold btn-lg w-full disabled:cursor-not-allowed disabled:opacity-60"
        data-track-click="Me pré-inscrire"
      >
        {loading ? "Envoi en cours…" : "Me pré-inscrire"}
      </button>
    </form>
  );
}
