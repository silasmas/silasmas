"use client";

import { useEffect, useState } from "react";
import { REGISTRATION_STATUS_STYLES } from "@/lib/registration-status";

interface RegistrationOpensCountdownProps {
  targetIso: string;
  targetLabel: string;
  /** compact : modale promo · default : page pré-inscription */
  variant?: "default" | "compact";
}

interface CountdownParts {
  days: number;
  hours: number;
  minutes: number;
  seconds: number;
  opened: boolean;
}

/**
 * Calcule le temps restant avant l'ouverture des inscriptions.
 */
function getParts(targetIso: string): CountdownParts {
  const target = new Date(targetIso).getTime();
  const now = Date.now();
  const diff = target - now;

  if (diff <= 0) {
    return { days: 0, hours: 0, minutes: 0, seconds: 0, opened: true };
  }

  const seconds = Math.floor(diff / 1000);
  const days = Math.floor(seconds / 86400);
  const hours = Math.floor((seconds % 86400) / 3600);
  const minutes = Math.floor((seconds % 3600) / 60);
  const secs = seconds % 60;

  return { days, hours, minutes, seconds: secs, opened: false };
}

/**
 * Compte à rebours jusqu'à l'ouverture officielle des inscriptions.
 */
export function RegistrationOpensCountdown({
  targetIso,
  targetLabel,
  variant = "default",
}: RegistrationOpensCountdownProps) {
  const [parts, setParts] = useState(() => getParts(targetIso));
  const isCompact = variant === "compact";

  useEffect(() => {
    const timer = window.setInterval(() => {
      setParts(getParts(targetIso));
    }, 1000);

    return () => {
      window.clearInterval(timer);
    };
  }, [targetIso]);

  if (parts.opened) {
    return (
      <div
        className={`rounded-xl text-center ${REGISTRATION_STATUS_STYLES.success} ${
          isCompact ? "px-4 py-3" : "card-lg rounded-2xl px-6 py-8"
        }`}
      >
        <p className={`font-semibold ${isCompact ? "text-sm" : "text-lg"}`}>
          Les inscriptions sont ouvertes
        </p>
        {!isCompact && (
          <p className="mt-2 text-sm opacity-90">Depuis le {targetLabel}</p>
        )}
      </div>
    );
  }

  const cells = [
    { label: "Jours", value: parts.days },
    { label: "Heures", value: parts.hours },
    { label: "Min.", value: parts.minutes },
    { label: "Sec.", value: parts.seconds },
  ];

  return (
    <div
      className={
        isCompact
          ? "rounded-xl border border-academy/20 bg-academy-soft/40 px-3 py-3"
          : "card-lg rounded-2xl border border-academy/20 bg-academy-soft/40 px-4 py-6"
      }
    >
      <p className={`mb-3 text-center text-muted ${isCompact ? "text-xs" : "text-sm"}`}>
        Ouverture le <strong className="text-academy">{targetLabel}</strong>
      </p>
      <div className={`grid grid-cols-4 ${isCompact ? "gap-1.5" : "gap-2 sm:gap-4"}`}>
        {cells.map((cell) => (
          <div
            key={cell.label}
            className={`rounded-lg border border-line bg-bg-elev text-center ${
              isCompact ? "px-1 py-2" : "rounded-xl px-2 py-3"
            }`}
          >
            <span
              className={`block font-bold tabular-nums text-academy ${
                isCompact ? "text-lg" : "text-2xl sm:text-3xl"
              }`}
            >
              {String(cell.value).padStart(2, "0")}
            </span>
            <span className="text-[10px] text-muted sm:text-xs">{cell.label}</span>
          </div>
        ))}
      </div>
    </div>
  );
}
