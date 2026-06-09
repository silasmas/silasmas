import { testimonials as staticTestimonials } from "@/lib/content";
import { Container } from "./Container";
import { Eyebrow } from "./Eyebrow";
import { Reveal } from "./Reveal";

type Testimonial = { quote: string; author: string; role?: string | null };

/**
 * Section témoignages — contenu CMS ou statique.
 */
export function Testimonials({
  testimonials = staticTestimonials,
}: {
  testimonials?: Testimonial[];
}) {
  return (
    <section className="py-24 md:py-32">
      <Container>
        <div className="max-w-2xl">
          <Eyebrow>Ce qu&apos;on dit de nous</Eyebrow>
          <h2 className="font-display mt-4 text-4xl leading-[1.05] tracking-tight md:text-5xl">
            Des collaborations <span className="italic">durables.</span>
          </h2>
        </div>

        <div className="mt-14 grid gap-5 md:grid-cols-3">
          {testimonials.map((testimonial, index) => (
            <Reveal key={testimonial.author} delay={index * 0.06}>
              <figure className="card-lg flex h-full flex-col p-7 md:p-8">
                <span aria-hidden className="font-display text-5xl leading-none text-accent">
                  &ldquo;
                </span>
                <blockquote className="mt-3 text-lg leading-relaxed text-ink-soft md:text-xl">
                  {testimonial.quote}
                </blockquote>
                <figcaption className="mt-auto pt-8">
                  <p className="text-sm font-medium text-ink">{testimonial.author}</p>
                  {testimonial.role && (
                    <p className="text-sm text-muted">{testimonial.role}</p>
                  )}
                </figcaption>
              </figure>
            </Reveal>
          ))}
        </div>
      </Container>
    </section>
  );
}
