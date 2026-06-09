import Link from "next/link";
import { ArrowUpRight } from "lucide-react";
import { Container } from "./Container";
import { Eyebrow } from "./Eyebrow";
import { Reveal } from "./Reveal";

type Pillar = {
  id: string;
  number: string;
  title: string;
  tagline: string;
  description: string;
  href: string;
  cta: string;
  tone: "ink" | "accent" | "academy";
  bullets: string[];
};

const pillars: Pillar[] = [
  {
    id: "silas",
    number: "01",
    title: "Silas",
    tagline: "Le consultant",
    description:
      "Stratégie produit, accompagnement de dirigeants et conférences. La voix derrière la marque.",
    href: "/silas",
    cta: "Travailler avec Silas",
    tone: "ink",
    bullets: [
      "Conseil stratégique 1-1",
      "Cadrage produit, audit digital",
      "Conférences & masterclasses",
    ],
  },
  {
    id: "studio",
    number: "02",
    title: "Studio",
    tagline: "L'atelier numérique",
    description:
      "L'équipe qui conçoit, design et développe vos produits — du MVP à la mise à l'échelle.",
    href: "/studio",
    cta: "Découvrir le studio",
    tone: "accent",
    bullets: [
      "Produits web & mobile",
      "Design system, UI/UX",
      "Marketing & IA appliquée",
    ],
  },
  {
    id: "academy",
    number: "03",
    title: "SDev Academy",
    tagline: "L'académie",
    description:
      "La nouvelle école pour devenir développeur produit, à l'ère de l'IA. Cohortes hybrides à Kinshasa.",
    href: "/academy",
    cta: "Rejoindre l'Academy",
    tone: "academy",
    bullets: [
      "Programmes intensifs 12 semaines",
      "Mentorat et projets réels",
      "Certificat & réseau d'alumni",
    ],
  },
];

export function PillarsGrid() {
  return (
    <section id="pillars" className="py-24 md:py-32">
      <Container>
        <div className="flex flex-col items-start justify-between gap-8 md:flex-row md:items-end">
          <div className="max-w-2xl">
            <Eyebrow>Trois piliers, une vision</Eyebrow>
            <h2 className="font-display mt-4 text-4xl leading-[1.05] tracking-tight md:text-6xl">
              Un écosystème pour
              <br />
              <span className="italic text-accent">conseiller, construire,</span>{" "}
              transmettre.
            </h2>
          </div>
          <p className="max-w-md text-base text-muted md:text-lg">
            Silas Développe est plus qu&apos;une agence : c&apos;est une marque
            personnelle, un studio et une académie qui partagent la même
            obsession de la qualité.
          </p>
        </div>

        <div className="mt-14 grid gap-4 md:grid-cols-3 md:gap-5">
          {pillars.map((p, i) => (
            <Reveal key={p.id} delay={i * 0.06}>
              <PillarCard pillar={p} />
            </Reveal>
          ))}
        </div>
      </Container>
    </section>
  );
}

function PillarCard({ pillar }: { pillar: Pillar }) {
  const isInk = pillar.tone === "ink";
  const isAcademy = pillar.tone === "academy";

  return (
    <Link
      href={pillar.href}
      className={[
        "group relative flex h-full flex-col overflow-hidden rounded-[28px] border p-7 transition-all duration-300 md:p-8",
        isInk
          ? "surface-strong surface-strong-hover border-transparent"
          : isAcademy
            ? "border-line bg-academy-soft text-ink hover:border-academy"
            : "border-line bg-bg-elev text-ink hover:border-accent",
      ].join(" ")}
    >
      <div className="flex items-center justify-between">
        <span
          className={[
            "eyebrow",
            isInk ? "text-[color:var(--strong-fg-soft)]" : "",
          ].join(" ")}
        >
          {pillar.number} — {pillar.tagline}
        </span>
        <span
          className={[
            "inline-flex size-9 items-center justify-center rounded-full border transition-transform duration-300 group-hover:-translate-y-0.5 group-hover:translate-x-0.5",
            isInk
              ? "border-[color:var(--strong-line)] bg-[color:var(--strong-line)]"
              : "border-line bg-bg",
          ].join(" ")}
          aria-hidden
        >
          <ArrowUpRight className="size-4" />
        </span>
      </div>

      <h3
        className={[
          "font-display mt-12 text-5xl tracking-tight md:text-6xl",
          isAcademy ? "text-academy" : "",
        ].join(" ")}
      >
        {pillar.title}
      </h3>

      <p
        className={[
          "mt-4 max-w-sm text-[1.02rem] leading-relaxed",
          isInk ? "text-[color:var(--strong-fg-muted)]" : "text-muted",
        ].join(" ")}
      >
        {pillar.description}
      </p>

      <ul
        className={[
          "mt-8 space-y-2 text-sm",
          isInk ? "text-[color:var(--strong-fg-muted)]" : "text-ink-soft",
        ].join(" ")}
      >
        {pillar.bullets.map((b) => (
          <li key={b} className="flex items-start gap-2">
            <span
              className={[
                "mt-1 inline-block size-1.5 shrink-0 rounded-full",
                isInk
                  ? "bg-[color:var(--strong-fg-soft)]"
                  : isAcademy
                    ? "bg-academy"
                    : "bg-accent",
              ].join(" ")}
            />
            {b}
          </li>
        ))}
      </ul>

      <span className="mt-10 inline-flex items-center gap-1.5 text-sm font-medium">
        {pillar.cta}
        <ArrowUpRight className="size-4 transition-transform duration-300 group-hover:-translate-y-0.5 group-hover:translate-x-0.5" />
      </span>
    </Link>
  );
}
