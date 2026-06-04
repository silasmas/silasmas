"use client";

import { Suspense } from "react";
import { PaymentReturnAlert } from "@/components/academy/PaymentReturnAlert";
import { RegistrationForm } from "@/components/academy/RegistrationForm";
import type { TrainingSession } from "@/types/api";

interface AcademySessionRegistrationProps {
  session: TrainingSession;
}

/**
 * Bloc inscription client (alerte retour paiement + formulaire wizard).
 */
export function AcademySessionRegistration({ session }: AcademySessionRegistrationProps) {
  return (
    <>
      <Suspense fallback={null}>
        <PaymentReturnAlert />
      </Suspense>
      <RegistrationForm session={session} />
    </>
  );
}
