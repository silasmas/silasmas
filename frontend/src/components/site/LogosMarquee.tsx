import { DEFAULT_CLIENT_LOGOS } from "@/lib/site-content";
import { Marquee } from "./Marquee";

/**
 * Bandeau défilant des clients (CMS ou liste par défaut).
 */
export function LogosMarquee({ logos = DEFAULT_CLIENT_LOGOS }: { logos?: string[] }) {
  return (
    <section
      aria-label="Ils nous font confiance"
      className="border-y border-line bg-bg-elev py-6"
    >
      <Marquee>
        {logos.map((name) => (
          <div
            key={name}
            className="font-display text-2xl tracking-tight text-muted whitespace-nowrap md:text-3xl"
          >
            {name}
            <span className="mx-12 inline-block size-1.5 translate-y-[-3px] rounded-full bg-line-strong align-middle" />
          </div>
        ))}
      </Marquee>
    </section>
  );
}
