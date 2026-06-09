"use client";

import Image from "next/image";
import Link from "next/link";
import { cn } from "@/lib/cn";
import { siteLogoUrl, useSiteSettings } from "@/components/providers/SiteSettingsProvider";

/**
 * Logo du site — monogramme SD ou image dynamique depuis l'API.
 */
export function Logo({
  className,
  showWordmark = true,
}: {
  className?: string;
  showWordmark?: boolean;
}) {
  const settings = useSiteSettings();
  const logoSrc = siteLogoUrl(settings);
  const isRemoteLogo = logoSrc.startsWith("http");

  return (
    <Link
      href="/"
      aria-label={`${settings.site_title} — accueil`}
      className={cn(
        "group inline-flex items-center gap-2.5 font-display text-lg tracking-tight",
        className
      )}
    >
      {isRemoteLogo ? (
        // eslint-disable-next-line @next/next/no-img-element
        <img
          src={logoSrc}
          alt={settings.site_title}
          className="h-9 w-auto object-contain"
        />
      ) : logoSrc.startsWith("/") ? (
        <Image
          src={logoSrc}
          alt={settings.site_title}
          width={36}
          height={36}
          className="h-9 w-auto object-contain"
          priority
        />
      ) : (
        <span className="relative inline-flex size-9 items-center justify-center rounded-lg border border-line bg-bg-elev text-[0.78rem] font-semibold tracking-[0.04em] text-ink transition-colors group-hover:border-line-strong">
          <span className="leading-none">SD</span>
          <span className="absolute -right-0.5 -top-0.5 size-1.5 rounded-full bg-accent" />
        </span>
      )}
      {showWordmark && (
        <span className="hidden text-ink sm:inline">
          {settings.site_title.split(" ")[0]}{" "}
          <span className="text-muted">
            {settings.site_title.split(" ").slice(1).join(" ") || "Développe"}
          </span>
        </span>
      )}
    </Link>
  );
}
