"use client";

import { Suspense } from "react";
import { PaymentReturnAlert } from "@/components/academy/PaymentReturnAlert";
import { RegistrationForm } from "@/components/academy/RegistrationForm";
import type { TrainingSession } from "@/types/api";

interface AcademySessionRegistrationProps {
  session: TrainingSession;
  /** hero : formulaire plus large et imposant sur la page session. */
  variant?: "default" | "hero";
}

/**
 * Bloc inscription client (alerte retour paiement + formulaire wizard).
 */
export function AcademySessionRegistration({
  session,
  variant = "default",
}: AcademySessionRegistrationProps) {
  return (
    <div id="inscription" className="scroll-mt-28">
      <Suspense fallback={null}>
        <PaymentReturnAlert />
      </Suspense>
      <RegistrationForm session={session} variant={variant} />
    </div>
  );
}
