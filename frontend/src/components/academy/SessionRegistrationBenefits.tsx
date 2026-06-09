import { CheckCircle2 } from "lucide-react";

interface SessionRegistrationBenefitsProps {
  benefits: string[];
  /** inline : sous les boutons dans la carte formulaire · modal : modale promo */
  variant?: "inline" | "modal" | "card";
}

/**
 * Affiche les avantages dynamiques d'une session (Filament).
 */
export function SessionRegistrationBenefits({
  benefits,
  variant = "card",
}: SessionRegistrationBenefitsProps) {
  if (benefits.length === 0) {
    return null;
  }

  if (variant === "inline") {
    return (
      <div className="mt-8 w-full border-t border-line pt-6">
        <p className="eyebrow mb-4">Avantages inclus</p>
        <ul className="grid w-full gap-3 sm:grid-cols-2">
          {benefits.map((benefit) => (
            <li
              key={benefit}
              className="flex w-full items-start gap-3 text-sm text-ink-soft md:text-base"
            >
              <CheckCircle2 className="mt-0.5 size-5 shrink-0 text-academy" />
              <span>{benefit}</span>
            </li>
          ))}
        </ul>
      </div>
    );
  }

  if (variant === "modal") {
    return (
      <div className="mb-5 w-full">
        <p className="eyebrow mb-3">Avantages inclus</p>
        <ul className="space-y-2">
          {benefits.map((benefit) => (
            <li
              key={benefit}
              className="flex items-start gap-2 text-sm text-ink-soft"
            >
              <CheckCircle2 className="mt-0.5 size-4 shrink-0 text-academy" />
              <span>{benefit}</span>
            </li>
          ))}
        </ul>
      </div>
    );
  }

  return (
    <div className="card-lg mt-6 w-full p-6 md:mt-8 md:p-8">
      <p className="eyebrow mb-5">Avantages inclus</p>
      <ul className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        {benefits.map((benefit) => (
          <li
            key={benefit}
            className="flex items-start gap-3 rounded-xl border border-line bg-bg px-4 py-3 text-sm text-ink-soft md:text-base"
          >
            <CheckCircle2 className="mt-0.5 size-5 shrink-0 text-academy" />
            <span>{benefit}</span>
          </li>
        ))}
      </ul>
    </div>
  );
}
