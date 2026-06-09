import type { Metadata } from "next";
import Link from "next/link";
import { Mail, Phone, MapPin, ArrowUpRight } from "lucide-react";
import { Container } from "@/components/site/Container";
import { Eyebrow } from "@/components/site/Eyebrow";
import { ContactForm } from "@/components/site/ContactForm";
import { site } from "@/lib/site";

export const metadata: Metadata = {
  title: "Contact",
  description:
    "Parlons de votre projet, de votre marque ou de votre prochaine cohorte.",
};

export default function ContactPage() {
  return (
    <>
      <section className="pt-12 pb-12 md:pt-20 md:pb-16">
        <Container>
          <Eyebrow>Contact</Eyebrow>
          <h1 className="font-display mt-6 max-w-4xl text-5xl leading-[1.02] tracking-tight md:text-7xl">
            Parlons de la suite.
            <br />
            <span className="italic text-accent">Sérieusement.</span>
          </h1>
          <p className="mt-7 max-w-2xl text-lg text-muted leading-relaxed md:text-xl">
            Une mission produit, une session de conseil, une inscription
            Academy ou simplement une question : nous lisons chaque message.
          </p>
        </Container>
      </section>

      <section className="pb-24 md:pb-32">
        <Container>
          <div className="grid gap-10 md:grid-cols-12 md:gap-12">
            <aside className="md:col-span-5">
              <div className="space-y-4">
                <ContactRow
                  icon={Mail}
                  label="Email"
                  value={site.email}
                  href={`mailto:${site.email}`}
                />
                <ContactRow
                  icon={Phone}
                  label="Téléphone"
                  value={`${site.phone} · ${site.phoneAlt}`}
                  href={`tel:${site.phone.replace(/\s/g, "")}`}
                />
                <ContactRow
                  icon={MapPin}
                  label="Adresse"
                  value={site.location}
                />
              </div>

              <div className="mt-10 card-lg p-7">
                <p className="eyebrow">Disponibilité</p>
                <p className="font-display mt-4 text-3xl tracking-tight">
                  Réponse sous 48h
                </p>
                <p className="mt-3 text-sm text-muted">
                  Du lundi au vendredi, 9h–18h (UTC+1).
                </p>
                <div className="mt-6 flex flex-wrap gap-3 text-sm">
                  <Link
                    href={site.socials.linkedin}
                    target="_blank"
                    rel="noreferrer"
                    className="inline-flex items-center gap-1.5 text-ink hover:text-accent"
                  >
                    LinkedIn <ArrowUpRight className="size-3.5" />
                  </Link>
                  <span className="text-line-strong">·</span>
                  <Link
                    href={site.socials.x}
                    target="_blank"
                    rel="noreferrer"
                    className="inline-flex items-center gap-1.5 text-ink hover:text-accent"
                  >
                    X <ArrowUpRight className="size-3.5" />
                  </Link>
                </div>
              </div>
            </aside>

            <div className="md:col-span-7">
              <ContactForm />
            </div>
          </div>
        </Container>
      </section>
    </>
  );
}

function ContactRow({
  icon: Icon,
  label,
  value,
  href,
}: {
  icon: React.ElementType;
  label: string;
  value: string;
  href?: string;
}) {
  const content = (
    <div className="flex items-start gap-4 rounded-xl border border-line bg-bg-elev p-5 transition-colors hover:border-line-strong">
      <span className="inline-flex size-10 shrink-0 items-center justify-center rounded-lg bg-accent-soft text-accent">
        <Icon className="size-4" />
      </span>
      <div>
        <p className="eyebrow">{label}</p>
        <p className="mt-1 text-base text-ink">{value}</p>
      </div>
    </div>
  );
  return href ? (
    <a href={href} className="block">
      {content}
    </a>
  ) : (
    content
  );
}
