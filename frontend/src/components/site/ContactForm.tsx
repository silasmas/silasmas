"use client";

import { FormEvent, useState } from "react";
import { ArrowRight, CheckCircle2, Loader2 } from "lucide-react";
import { submitContact } from "@/lib/api";

const intents = [
  "Agence — projet web ou mobile",
  "Agence — design & branding",
  "Conseil 1-1 avec Silas",
  "SDev Academy — inscription",
  "Autre",
];

/**
 * Formulaire de contact connecté à l'API Laravel.
 */
export function ContactForm() {
  const [submitted, setSubmitted] = useState(false);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);

  const handleSubmit = async (event: FormEvent<HTMLFormElement>) => {
    event.preventDefault();
    setLoading(true);
    setError(null);

    const form = event.currentTarget;
    const data = new FormData(form);

    try {
      const response = await submitContact({
        nom: String(data.get("name") ?? ""),
        email: String(data.get("email") ?? ""),
        phone: String(data.get("phone") ?? ""),
        subject: String(data.get("intent") ?? intents[0]),
        message: String(data.get("message") ?? ""),
      });

      if (!response.success) {
        setError(response.message ?? "Envoi impossible. Réessayez.");
        return;
      }

      setSubmitted(true);
      form.reset();
    } catch {
      setError("Erreur réseau. Vérifiez votre connexion.");
    } finally {
      setLoading(false);
    }
  };

  return (
    <form onSubmit={handleSubmit} className="card-lg space-y-6 p-7 md:p-8">
      <div className="grid gap-5 md:grid-cols-2">
        <Field label="Nom" name="name" placeholder="Votre nom" required />
        <Field
          label="Email"
          name="email"
          type="email"
          placeholder="vous@email.com"
          required
        />
      </div>
      <Field
        label="Téléphone"
        name="phone"
        type="tel"
        placeholder="+243 ..."
      />
      <Field
        label="Entreprise / projet"
        name="company"
        placeholder="Optionnel"
      />

      <div>
        <label className="eyebrow" htmlFor="intent">
          Je viens pour
        </label>
        <select
          id="intent"
          name="intent"
          className="mt-2 h-12 w-full rounded-xl border border-line bg-bg-elev px-4 text-base text-ink outline-none transition-colors focus:border-ink"
          defaultValue={intents[0]}
        >
          {intents.map((intent) => (
            <option key={intent}>{intent}</option>
          ))}
        </select>
      </div>

      <div>
        <label className="eyebrow" htmlFor="message">
          Message
        </label>
        <textarea
          id="message"
          name="message"
          rows={5}
          required
          placeholder="Parlez-nous de votre projet, de vos enjeux, de votre échéance."
          className="mt-2 w-full rounded-xl border border-line bg-bg-elev p-4 text-base text-ink outline-none transition-colors focus:border-ink"
        />
      </div>

      {error && (
        <p className="rounded-xl border border-accent/30 bg-accent-soft p-4 text-sm text-ink">
          {error}
        </p>
      )}

      <div className="flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">
        <p className="text-xs text-muted">
          En envoyant ce message, vous acceptez d&apos;être contacté·e par notre équipe.
        </p>
        <button
          type="submit"
          disabled={loading}
          className="surface-strong surface-strong-hover inline-flex h-12 items-center gap-2 rounded-full px-6 text-base transition-colors disabled:opacity-60"
        >
          {loading ? (
            <>
              Envoi...
              <Loader2 className="size-4 animate-spin" />
            </>
          ) : submitted ? (
            <>
              Envoyé
              <CheckCircle2 className="size-4 text-accent" />
            </>
          ) : (
            <>
              Envoyer le message
              <ArrowRight className="size-4" />
            </>
          )}
        </button>
      </div>

      {submitted && (
        <p className="rounded-xl border border-line bg-bg p-4 text-sm text-ink-soft">
          Merci, nous revenons vers vous sous 48 heures ouvrées.
        </p>
      )}
    </form>
  );
}

function Field({
  label,
  name,
  type = "text",
  placeholder,
  required,
}: {
  label: string;
  name: string;
  type?: string;
  placeholder?: string;
  required?: boolean;
}) {
  return (
    <div>
      <label className="eyebrow" htmlFor={name}>
        {label}
      </label>
      <input
        id={name}
        name={name}
        type={type}
        placeholder={placeholder}
        required={required}
        className="mt-2 h-12 w-full rounded-xl border border-line bg-bg-elev px-4 text-base text-ink outline-none transition-colors focus:border-ink"
      />
    </div>
  );
}
