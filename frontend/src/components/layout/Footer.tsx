"use client";

import Image from "next/image";
import Link from "next/link";
import { siteLogoUrl, useSiteSettings } from "@/components/providers/SiteSettingsProvider";

/**
 * Pied de page dynamique du site SDev.
 */
export function Footer() {
  const settings = useSiteSettings();
  const logoSrc = siteLogoUrl(settings);
  const isRemoteLogo = logoSrc.startsWith("http");

  return (
    <footer className="border-t border-[var(--color-border)] bg-[var(--color-bg-2)] py-12">
      <div className="container grid gap-10 md:grid-cols-[1.2fr_1fr_1fr]">
        <div>
          {isRemoteLogo ? (
            // eslint-disable-next-line @next/next/no-img-element
            <img src={logoSrc} alt={settings.site_title} className="mb-4 h-12 w-auto object-contain" />
          ) : (
            <Image
              src={logoSrc}
              alt={settings.site_title}
              width={160}
              height={56}
              className="mb-4 h-12 w-auto object-contain"
            />
          )}
          <p className="max-w-md text-sm text-muted">
            {settings.footer_description}
          </p>
        </div>

        <div>
          <h3 className="mb-4 font-semibold">Navigation</h3>
          <ul className="space-y-2 text-sm text-muted">
            <li><a href="/#about" className="hover:text-accent">À propos</a></li>
            <li><a href="/#services" className="hover:text-accent">Services</a></li>
            <li><a href="/#portfolio" className="hover:text-accent">Portfolio</a></li>
            <li><Link href="/#academy" className="hover:text-accent">SDev Academy</Link></li>
          </ul>
        </div>

        <div>
          <h3 className="mb-4 font-semibold">Contact</h3>
          <ul className="space-y-2 text-sm text-muted">
            {settings.email && <li>{settings.email}</li>}
            {settings.phone_primary && <li>{settings.phone_primary}</li>}
            {settings.phone_secondary && <li>{settings.phone_secondary}</li>}
            {settings.address && <li>{settings.address}</li>}
          </ul>
        </div>
      </div>

      <div className="container mt-10 border-t border-[var(--color-border)] pt-6 text-center text-sm text-muted">
        © {new Date().getFullYear()} {settings.site_title}. Tous droits réservés.
      </div>
    </footer>
  );
}
