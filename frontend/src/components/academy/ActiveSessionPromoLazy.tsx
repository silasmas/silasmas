"use client";

import dynamic from "next/dynamic";
import type { TrainingSession } from "@/types/api";

const ActiveSessionPromo = dynamic(
  () =>
    import("@/components/academy/ActiveSessionPromo").then(
      (module) => module.ActiveSessionPromo,
    ),
  { ssr: false },
);

interface ActiveSessionPromoLazyProps {
  session: TrainingSession | null;
}

/**
 * Charge la modale promo côté client uniquement (allège le premier rendu).
 */
export function ActiveSessionPromoLazy({ session }: ActiveSessionPromoLazyProps) {
  return <ActiveSessionPromo session={session} />;
}
