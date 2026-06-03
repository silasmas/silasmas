"use client";

import { FormEvent, useState } from "react";
import { submitContact } from "@/lib/api";
import { CONTACT_INFO } from "@/data/site";

/**
 * Section contact avec formulaire branché à l'API Laravel.
 */
export function ContactSection() {
  const [loading, setLoading] = useState(false);
  const [feedback, setFeedback] = useState<{ type: "success" | "error"; text: string } | null>(null);

  /**
   * Soumet le formulaire de contact.
   */
  async function handleSubmit(event: FormEvent<HTMLFormElement>) {
    event.preventDefault();
    setLoading(true);
    setFeedback(null);

    const formData = new FormData(event.currentTarget);

    try {
      const result = await submitContact({
        nom: String(formData.get("nom") ?? ""),
        email: String(formData.get("email") ?? ""),
        phone: String(formData.get("phone") ?? ""),
        subject: String(formData.get("subject") ?? ""),
        message: String(formData.get("message") ?? ""),
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
        text: "Impossible d'envoyer le message. Réessayez plus tard.",
      });
    } finally {
      setLoading(false);
    }
  }

  return (
    <section id="contact" className="section section-dark">
      <div className="container">
        <div className="mb-12 text-center">
          <span className="section-eyebrow">Écrivez-nous</span>
          <h2 className="section-title">Contact</h2>
        </div>

        <div className="grid gap-10 lg:grid-cols-[1fr_1.4fr]">
          <div className="space-y-6">
            <div className="glass rounded-2xl p-5">
              <h3 className="mb-2 font-semibold text-amber-400">Adresse</h3>
              <p className="text-slate-300">{CONTACT_INFO.address}</p>
            </div>
            <div className="glass rounded-2xl p-5">
              <h3 className="mb-2 font-semibold text-amber-400">E-mail</h3>
              <p className="text-slate-300">{CONTACT_INFO.email}</p>
            </div>
            <div className="glass rounded-2xl p-5">
              <h3 className="mb-2 font-semibold text-amber-400">Téléphone</h3>
              {CONTACT_INFO.phones.map((phone) => (
                <p key={phone} className="text-slate-300">{phone}</p>
              ))}
            </div>
          </div>

          <form onSubmit={handleSubmit} className="glass space-y-4 rounded-3xl p-6 md:p-8">
            <div className="grid gap-4 md:grid-cols-2">
              <input name="nom" required placeholder="Nom complet" className="w-full rounded-2xl border border-amber-500/15 bg-white/[0.03] px-4 py-3.5 text-white outline-none focus:border-amber-500/45" />
              <input name="email" type="email" required placeholder="E-mail" className="w-full rounded-2xl border border-amber-500/15 bg-white/[0.03] px-4 py-3.5 text-white outline-none focus:border-amber-500/45" />
              <input name="phone" required placeholder="Téléphone" className="w-full rounded-2xl border border-amber-500/15 bg-white/[0.03] px-4 py-3.5 text-white outline-none focus:border-amber-500/45" />
              <input name="subject" required placeholder="Sujet" className="w-full rounded-2xl border border-amber-500/15 bg-white/[0.03] px-4 py-3.5 text-white outline-none focus:border-amber-500/45" />
            </div>
            <textarea name="message" required rows={5} placeholder="Message" className="w-full rounded-2xl border border-amber-500/15 bg-white/[0.03] px-4 py-3.5 text-white outline-none focus:border-amber-500/45" />
            {feedback && (
              <p className={`text-sm ${feedback.type === "success" ? "text-green-400" : "text-red-400"}`}>
                {feedback.text}
              </p>
            )}
            <button type="submit" disabled={loading} className="btn btn-gold btn-lg w-full md:w-auto">
              {loading ? "Envoi..." : "Envoyer message"}
            </button>
          </form>
        </div>
      </div>
    </section>
  );
}
