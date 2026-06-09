import { principles as staticPrinciples } from "@/lib/content";
import { Container } from "./Container";
import { Eyebrow } from "./Eyebrow";
import { Reveal } from "./Reveal";

type Principle = { title: string; body: string };

/**
 * Section manifeste — principes depuis CMS ou contenu statique.
 */
export function Manifesto({
  principles = staticPrinciples,
}: {
  principles?: Principle[];
}) {
  return (
    <section className="border-y border-line bg-bg-elev py-24 md:py-32">
      <Container>
        <div className="grid gap-14 md:grid-cols-12 md:gap-16">
          <div className="md:col-span-5">
            <Eyebrow>Notre manifeste</Eyebrow>
            <h2 className="font-display mt-4 text-4xl leading-[1.05] tracking-tight md:text-5xl">
              Quatre principes,
              <br />
              <span className="italic">tenus avec rigueur.</span>
            </h2>
            <p className="mt-6 max-w-md text-base text-muted leading-relaxed md:text-lg">
              Pas de buzzwords ni de prouesses gratuites. Une exigence simple,
              transmise à chaque projet et à chaque cohorte.
            </p>
          </div>

          <ul className="md:col-span-7 divide-y divide-line border-y border-line">
            {principles.map((principle, index) => (
              <Reveal as="li" key={principle.title} delay={index * 0.05}>
                <div className="grid grid-cols-12 gap-4 py-7 md:py-9">
                  <div className="col-span-2 md:col-span-1">
                    <span className="font-mono text-xs text-muted">
                      0{index + 1}
                    </span>
                  </div>
                  <div className="col-span-10 md:col-span-11">
                    <h3 className="font-display text-2xl tracking-tight md:text-3xl">
                      {principle.title}
                    </h3>
                    <p className="mt-2 max-w-xl text-base text-muted leading-relaxed">
                      {principle.body}
                    </p>
                  </div>
                </div>
              </Reveal>
            ))}
          </ul>
        </div>
      </Container>
    </section>
  );
}
