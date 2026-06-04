"use client";

import { useEffect, useState } from "react";

interface HeroSectionProps {
  taglines: string[];
}

/**
 * Section hero avec animation des expertises (contenu dynamique API).
 */
export function HeroSection({ taglines }: HeroSectionProps) {
  const items = taglines.length > 0 ? taglines : ["Solutions numériques"];
  const [index, setIndex] = useState(0);

  useEffect(() => {
    const timer = setInterval(() => {
      setIndex((current) => (current + 1) % items.length);
    }, 2500);

    return () => clearInterval(timer);
  }, [items.length]);

  return (
    <section id="hero" className="relative min-h-[92vh] overflow-hidden">
      <div className="pointer-events-none absolute -left-32 top-20 h-72 w-72 rounded-full bg-amber-500/10 blur-3xl" />
      <div className="pointer-events-none absolute -right-20 bottom-10 h-80 w-80 rounded-full bg-orange-600/10 blur-3xl" />

      <div className="container grid min-h-[92vh] items-center gap-12 py-16 lg:grid-cols-2">
        <div>
          <span className="section-eyebrow">Bienvenue chez SDEV</span>
          <h1 className="section-title">
            Silas <span className="text-gold">Développe</span>
          </h1>
          <p className="mb-8 max-w-xl text-lg text-muted">
            Expertise en{" "}
            <span className="font-semibold text-accent">{items[index]}</span>
          </p>
          <div className="flex flex-wrap gap-4">
            <a href="#portfolio" className="btn btn-gold btn-lg">
              Voir le portfolio
            </a>
            <a href="#academy" className="btn btn-outline btn-lg">
              SDev Academy
            </a>
          </div>
        </div>

        <div className="relative hidden lg:block">
          <div className="glass relative overflow-hidden rounded-3xl p-8">
            <div className="absolute inset-0 bg-gradient-to-br from-amber-500/10 to-transparent" />
            <div className="relative space-y-4">
              <div className="rounded-2xl border border-[var(--color-border)] bg-[var(--color-bg-card)] p-5">
                <p className="text-sm uppercase tracking-widest text-accent">SDev Academy</p>
                <p className="mt-2 text-xl font-semibold">Formation numérique en RDC & Afrique</p>
              </div>
              <div className="grid grid-cols-2 gap-4">
                {["Web & Mobile", "Design", "Marketing", "Formation IA"].map((item) => (
                  <div
                    key={item}
                    className="rounded-xl border border-[var(--color-border)] bg-[var(--color-bg-card)] p-4 text-sm"
                  >
                    {item}
                  </div>
                ))}
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  );
}
