"use client";

import { Compass, Lightbulb, Mic, type LucideIcon } from "lucide-react";
import { Reveal } from "./Reveal";
import type { SilasOffer } from "@/types/api";

const OFFER_ICONS: Record<string, LucideIcon> = {
  compass: Compass,
  lightbulb: Lightbulb,
  mic: Mic,
};

/**
 * Grille des offres conseil Silas avec icônes dynamiques.
 */
export function SilasOffers({ offers }: { offers: SilasOffer[] }) {
  return (
    <div className="mt-12 grid gap-4 md:grid-cols-3 md:gap-5">
      {offers.map((offer, index) => {
        const Icon = OFFER_ICONS[offer.icon] ?? Compass;

        return (
          <Reveal key={offer.id ?? offer.title} delay={index * 0.05}>
            <div className="card-lg flex h-full flex-col p-7">
              <span className="inline-flex size-11 items-center justify-center rounded-full bg-accent-soft text-accent">
                <Icon className="size-5" />
              </span>
              <h3 className="font-display mt-8 text-2xl tracking-tight md:text-3xl">
                {offer.title}
              </h3>
              <p className="mt-3 text-base text-muted leading-relaxed">
                {offer.body}
              </p>
            </div>
          </Reveal>
        );
      })}
    </div>
  );
}
