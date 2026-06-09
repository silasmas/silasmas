"use client";

import { useEffect, useState } from "react";
import { REGISTRATION_STATUS_STYLES } from "@/lib/registration-status";

interface ParticipantCountdownProps {
  targetIso: string;
  startDateLabel: string;
}

interface CountdownParts {
  days: number;
  hours: number;
  minutes: number;
  seconds: number;
  started: boolean;
}

/**
 * Calcule le temps restant avant la formation.
 */
function getParts(targetIso: string): CountdownParts {
  const target = new Date(targetIso).getTime();
  const now = Date.now();
  const diff = target - now;

  if (diff <= 0) {
    return { days: 0, hours: 0, minutes: 0, seconds: 0, started: true };
  }

  const seconds = Math.floor(diff / 1000);
  const days = Math.floor(seconds / 86400);
  const hours = Math.floor((seconds % 86400) / 3600);
  const minutes = Math.floor((seconds % 3600) / 60);
  const secs = seconds % 60;

  return { days, hours, minutes, seconds: secs, started: false };
}

/**
 * Compte à rebours jusqu'au début de la session.
 */
export function ParticipantCountdown({
  targetIso,
  startDateLabel,
}: ParticipantCountdownProps) {
  const [parts, setParts] = useState(() => getParts(targetIso));

  useEffect(() => {
    const timer = window.setInterval(() => {
      setParts(getParts(targetIso));
    }, 1000);

    return () => {
      window.clearInterval(timer);
    };
  }, [targetIso]);

  if (parts.started) {
    return (
      <div className={`card-lg rounded-2xl px-6 py-8 text-center ${REGISTRATION_STATUS_STYLES.success}`}>
        <p className="text-lg font-semibold">La formation a commencé</p>
        <p className="mt-2 text-sm opacity-90">Depuis le {startDateLabel}</p>
      </div>
    );
  }

  const cells = [
    { label: "Jours", value: parts.days },
    { label: "Heures", value: parts.hours },
    { label: "Minutes", value: parts.minutes },
    { label: "Secondes", value: parts.seconds },
  ];

  return (
    <div className="card-lg rounded-2xl border border-academy/20 bg-academy-soft/40 px-4 py-6">
      <p className="mb-4 text-center text-sm text-muted">
        Début prévu le <strong className="text-academy">{startDateLabel}</strong>
      </p>
      <div className="grid grid-cols-4 gap-2 sm:gap-4">
        {cells.map((cell) => (
          <div
            key={cell.label}
            className="rounded-xl border border-line bg-bg-elev px-2 py-3 text-center"
          >
            <span className="block text-2xl font-bold tabular-nums text-academy sm:text-3xl">
              {String(cell.value).padStart(2, "0")}
            </span>
            <span className="text-xs text-muted">{cell.label}</span>
          </div>
        ))}
      </div>
    </div>
  );
}
