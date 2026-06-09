import Link from "next/link";
import { ArrowRight } from "lucide-react";
import { Container } from "./Container";
import { Eyebrow } from "./Eyebrow";

export function CTA({
  title = "On en parle ?",
  subtitle = "Une idée, un projet, une cohorte à intégrer ? Écrivez-nous.",
  cta = "Démarrer un projet",
  href = "/contact",
}: {
  title?: string;
  subtitle?: string;
  cta?: string;
  href?: string;
}) {
  return (
    <section className="py-24 md:py-32">
      <Container size="narrow">
        <div className="text-center">
          <Eyebrow>Prochaine étape</Eyebrow>
          <h2 className="font-display mt-4 text-5xl leading-[1.05] tracking-tight md:text-7xl">
            {title}
          </h2>
          <p className="mx-auto mt-6 max-w-xl text-lg text-muted md:text-xl">
            {subtitle}
          </p>
          <Link
            href={href}
            className="surface-strong surface-strong-hover mt-10 inline-flex h-14 items-center gap-2 rounded-full px-8 text-base transition-colors"
          >
            {cta}
            <ArrowRight className="size-5" />
          </Link>
        </div>
      </Container>
    </section>
  );
}
