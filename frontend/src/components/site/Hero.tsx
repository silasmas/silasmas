"use client";

import Link from "next/link";
import Image from "next/image";
import { motion } from "framer-motion";
import { ArrowRight, ArrowUpRight } from "lucide-react";
import { Container } from "./Container";
import { Eyebrow } from "./Eyebrow";
import { stock } from "@/lib/stock";
import { DEFAULT_HERO } from "@/lib/site-content";

export type HeroContent = {
  eyebrow: string;
  headline: string;
  headlineAccent: string;
  headlineSuffix: string;
  body: string;
  image?: string | null;
};

/**
 * Section hero de la page d'accueil (contenu CMS ou défaut sdev).
 */
export function Hero({ content = DEFAULT_HERO }: { content?: HeroContent }) {
  const portrait = content.image ?? stock.hero.portrait;

  return (
    <section className="relative overflow-hidden pt-12 pb-24 md:pt-20 md:pb-36">
      <div
        aria-hidden
        className="pointer-events-none absolute -left-40 -top-40 size-[640px] rounded-full opacity-60 blur-3xl"
        style={{
          background:
            "radial-gradient(closest-side, rgba(255,91,31,0.32), rgba(255,91,31,0))",
        }}
      />
      <div
        aria-hidden
        className="pointer-events-none absolute -right-40 top-40 size-[520px] rounded-full opacity-50 blur-3xl"
        style={{
          background:
            "radial-gradient(closest-side, rgba(180,83,9,0.22), rgba(180,83,9,0))",
        }}
      />

      <Container className="relative">
        <div className="grid grid-cols-1 items-end gap-12 lg:grid-cols-12">
          <div className="lg:col-span-8">
            <motion.div
              initial={{ opacity: 0, y: 14 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ duration: 0.6 }}
            >
              <Eyebrow>{content.eyebrow}</Eyebrow>
            </motion.div>

            <motion.h1
              initial={{ opacity: 0, y: 18 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ duration: 0.7, delay: 0.05 }}
              className="font-display mt-6 text-[2.6rem] leading-[1.02] tracking-tight md:text-7xl lg:text-[5.5rem]"
            >
              {content.headline}{" "}
              <span className="text-accent italic">{content.headlineAccent}</span>{" "}
              {content.headlineSuffix}
            </motion.h1>

            <motion.p
              initial={{ opacity: 0, y: 18 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ duration: 0.7, delay: 0.15 }}
              className="mt-7 max-w-2xl text-lg text-muted leading-relaxed md:text-xl"
            >
              {content.body}
            </motion.p>

            <motion.div
              initial={{ opacity: 0, y: 14 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ duration: 0.6, delay: 0.25 }}
              className="mt-10 flex flex-wrap items-center gap-3"
            >
              <Link
                href="/contact"
                className="surface-strong surface-strong-hover inline-flex h-12 items-center gap-2 rounded-full px-6 text-base transition-colors"
              >
                Démarrer un projet
                <ArrowRight className="size-4" />
              </Link>
              <Link
                href="/portfolio"
                className="inline-flex h-12 items-center gap-2 rounded-full border border-line bg-bg-elev px-6 text-base text-ink transition-colors hover:border-line-strong"
              >
                Voir le portfolio
                <ArrowUpRight className="size-4" />
              </Link>
            </motion.div>
          </div>

          <motion.aside
            initial={{ opacity: 0, y: 24 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.7, delay: 0.3 }}
            className="lg:col-span-4"
            aria-label="Présentation"
          >
            <div className="relative aspect-[4/5] w-full overflow-hidden rounded-[28px] border border-line">
              <Image
                src={portrait}
                alt="Silas Masimango — au travail"
                fill
                priority
                sizes="(min-width: 1024px) 33vw, 100vw"
                className="object-cover"
              />
              <div
                aria-hidden
                className="absolute inset-0 bg-gradient-to-t from-ink/60 via-ink/10 to-transparent"
              />
              <div className="absolute inset-x-5 bottom-5 flex items-end justify-between gap-4 text-white">
                <div>
                  <p className="font-mono text-[0.7rem] uppercase tracking-[0.2em] text-white/70">
                    Agence · Kinshasa
                  </p>
                  <p className="font-display mt-1 text-2xl tracking-tight">
                    Silas Masimango
                  </p>
                </div>
                <span className="inline-flex size-9 items-center justify-center rounded-full bg-white/15 backdrop-blur-md">
                  <ArrowUpRight className="size-4" />
                </span>
              </div>
            </div>

            <dl className="mt-4 grid grid-cols-2 gap-3">
              {[
                { v: "10+", l: "Années" },
                { v: "40+", l: "Projets" },
                { v: "200+", l: "Clients satisfaits" },
                { v: "300+", l: "Apprenants" },
              ].map((item) => (
                <div
                  key={item.l}
                  className="card flex flex-col justify-between p-4"
                >
                  <dt className="eyebrow">{item.l}</dt>
                  <dd className="font-display mt-5 text-3xl tracking-tight md:text-4xl">
                    {item.v}
                  </dd>
                </div>
              ))}
            </dl>
          </motion.aside>
        </div>
      </Container>
    </section>
  );
}
