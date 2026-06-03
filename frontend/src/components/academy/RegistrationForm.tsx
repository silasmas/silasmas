"use client";

import { FormEvent, useState } from "react";
import { submitRegistration } from "@/lib/api";
import type { TrainingSession } from "@/types/api";

interface RegistrationFormProps {
  session: TrainingSession;
}

/**
 * Formulaire d'inscription à une session SDev Academy.
 */
export function RegistrationForm({ session }: RegistrationFormProps) {
  const [loading, setLoading] = useState(false);
  const [feedback, setFeedback] = useState<{ type: "success" | "error"; text: string } | null>(null);

  /**
   * Soumet l'inscription à l'API Laravel.
   */
  async function handleSubmit(event: FormEvent<HTMLFormElement>) {
    event.preventDefault();
    setLoading(true);
    setFeedback(null);

    const formData = new FormData(event.currentTarget);

    try {
      const result = await submitRegistration({
        training_session_slug: session.slug,
        firstname: String(formData.get("firstname") ?? ""),
        lastname: String(formData.get("lastname") ?? ""),
        email: String(formData.get("email") ?? ""),
        phone: String(formData.get("phone") ?? ""),
        city: String(formData.get("city") ?? ""),
        country: String(formData.get("country") ?? "RDC"),
        education_level: String(formData.get("education_level") ?? ""),
        motivation: String(formData.get("motivation") ?? ""),
        marketing_opt_in: formData.get("marketing_opt_in") === "on",
      });

      if (result.success) {
        setFeedback({ type: "success", text: result.message });
        event.currentTarget.reset();
      } else {
        setFeedback({ type: "error", text: result.message });
      }
    } catch {
      setFeedback({
        type: "error",
        text: "Inscription impossible pour le moment. Réessayez plus tard.",
      });
    } finally {
      setLoading(false);
    }
  }

  if (!session.accepts_registrations) {
    return (
      <div className="glass rounded-3xl p-8 text-center text-slate-300">
        Les inscriptions pour cette session sont fermées.
      </div>
    );
  }

  const inputClass =
    "w-full rounded-2xl border border-amber-500/15 bg-white/[0.03] px-4 py-3.5 text-white outline-none focus:border-amber-500/45";

  return (
    <form onSubmit={handleSubmit} className="glass space-y-4 rounded-3xl p-6 md:p-8">
      <h2 className="text-2xl font-bold">Formulaire d&apos;inscription</h2>
      <div className="grid gap-4 md:grid-cols-2">
        <input name="firstname" required placeholder="Prénom *" className={inputClass} />
        <input name="lastname" required placeholder="Nom *" className={inputClass} />
        <input name="email" type="email" required placeholder="E-mail *" className={inputClass} />
        <input name="phone" placeholder="Téléphone" className={inputClass} />
        <input name="city" placeholder="Ville" className={inputClass} />
        <input name="country" defaultValue="RDC" placeholder="Pays" className={inputClass} />
        <input name="education_level" placeholder="Niveau d'études" className={`${inputClass} md:col-span-2`} />
      </div>
      <textarea name="motivation" rows={4} placeholder="Motivation (optionnel)" className={inputClass} />
      <label className="flex items-start gap-3 text-sm text-slate-300">
        <input name="marketing_opt_in" type="checkbox" defaultChecked className="mt-1" />
        J&apos;accepte de recevoir les communications de SDev Academy sur les prochaines formations.
      </label>
      {feedback && (
        <p className={`text-sm ${feedback.type === "success" ? "text-green-400" : "text-red-400"}`}>
          {feedback.text}
        </p>
      )}
      <button type="submit" disabled={loading} className="btn btn-gold btn-lg">
        {loading ? "Inscription..." : "Confirmer mon inscription"}
      </button>
    </form>
  );
}
