import type { SiteService } from "@/types/api";

const ICONS: Record<string, string> = {
  globe: "🌐",
  mobile: "📱",
  marketing: "📣",
  design: "🎨",
};

interface ServicesSectionProps {
  services: SiteService[];
}

/**
 * Section services SDEV (contenu dynamique API).
 */
export function ServicesSection({ services }: ServicesSectionProps) {
  return (
    <section id="services" className="section">
      <div className="container">
        <div className="mb-12 text-center">
          <span className="section-eyebrow">Ce que nous faisons</span>
          <h2 className="section-title">Services</h2>
        </div>

        <div className="grid gap-6 sm:grid-cols-2 xl:grid-cols-4">
          {services.map((service) => (
            <article
              key={service.id}
              className="glass group rounded-3xl p-6 transition hover:-translate-y-1 hover:border-[var(--color-accent)]"
            >
              <div className="mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-amber-500/10 text-2xl">
                {ICONS[service.icon] ?? "✨"}
              </div>
              <h3 className="mb-3 text-xl font-semibold">{service.title}</h3>
              <p className="text-sm text-muted">{service.description}</p>
            </article>
          ))}
        </div>
      </div>
    </section>
  );
}
