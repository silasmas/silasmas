import Image from "next/image";
import { resolveStorageUrl } from "@/lib/api";
import type { SiteAbout } from "@/types/api";

interface AboutSectionProps {
  about: SiteAbout;
}

/**
 * Section à propos de SDEV (contenu dynamique API).
 */
export function AboutSection({ about }: AboutSectionProps) {
  const imageSrc =
    resolveStorageUrl(about.image) ?? "/images/logo.png";
  const isRemote = imageSrc.startsWith("http");

  return (
    <section id="about" className="section">
      <div className="container grid items-center gap-12 lg:grid-cols-[320px_1fr]">
        <div className="relative mx-auto w-full max-w-xs">
          <div className="absolute -inset-3 rounded-[2rem] bg-gradient-to-br from-amber-500/30 to-orange-600/10 blur-sm" />
          <div className="relative overflow-hidden rounded-[2rem] border border-[var(--color-border)] bg-[var(--color-bg-card)] p-6">
            <Image
              src={imageSrc}
              alt="À propos — Silas Développe"
              width={280}
              height={280}
              className="mx-auto h-auto w-full object-contain"
              unoptimized={isRemote}
            />
          </div>
        </div>

        <div>
          {about.eyebrow && <span className="section-eyebrow">{about.eyebrow}</span>}
          <h2 className="section-title">{about.title}</h2>
          {about.body && <p className="mb-4 text-lg text-muted">{about.body}</p>}
          {about.secondary_body && (
            <p className="text-muted">{about.secondary_body}</p>
          )}
        </div>
      </div>
    </section>
  );
}
