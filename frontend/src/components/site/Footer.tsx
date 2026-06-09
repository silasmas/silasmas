"use client";

import Link from "next/link";
import { ArrowUpRight } from "lucide-react";
import { Container } from "./Container";
import { Logo } from "./Logo";
import { useSiteSettings } from "@/components/providers/SiteSettingsProvider";
import { isAcademyLaunchMode } from "@/lib/launch";
import { site } from "@/lib/site";

const columns = [
  {
    title: "Explorer",
    links: [
      { href: "/silas", label: "Silas" },
      { href: "/studio", label: "Studio" },
      { href: "/academy", label: "Academy" },
      { href: "/portfolio", label: "Portfolio" },
    ],
  },
  {
    title: "Studio",
    links: [
      { href: "/studio#produit", label: "Produits web & mobile" },
      { href: "/studio#design", label: "Design produit" },
      { href: "/studio#marketing", label: "Marketing & contenu" },
      { href: "/studio#ia", label: "IA appliquée" },
    ],
  },
  {
    title: "Suivre",
    links: [
      { href: site.socials.linkedin, label: "LinkedIn", external: true },
      { href: site.socials.github, label: "GitHub", external: true },
      { href: site.socials.youtube, label: "YouTube", external: true },
      { href: site.socials.x, label: "X", external: true },
    ],
  },
];

interface FooterProps {
  /** Lien d'inscription résolu côté serveur (mode lancement). */
  registrationHref?: string;
}

/**
 * Pied de page du site avec contenu dynamique (API) et navigation sdev.
 */
export function Footer({ registrationHref }: FooterProps) {
  const settings = useSiteSettings();
  const launchMode = isAcademyLaunchMode();

  return (
    <footer className="mt-24 border-t border-line bg-bg-elev">
      <Container className="py-16 md:py-20">
        <div className="grid gap-12 md:grid-cols-12">
          <div className={launchMode ? "md:col-span-8" : "md:col-span-5"}>
            <Logo />
            <p className="mt-5 max-w-md text-base text-muted leading-relaxed">
              {launchMode
                ? "SDev Academy — formations en ligne pour développer avec l'intelligence artificielle."
                : (settings.footer_description
                  ?? "Silas Développe — studio numérique, conseil et SDev Academy à Kinshasa.")}
            </p>
            {settings.email && (
              <Link
                href={`mailto:${settings.email}`}
                className="mt-6 inline-flex items-center gap-2 text-sm font-medium text-ink hover:text-accent"
              >
                {settings.email}
                <ArrowUpRight className="size-4" />
              </Link>
            )}
            {launchMode && registrationHref && (
              <Link
                href={registrationHref}
                className="mt-4 inline-flex items-center gap-2 text-sm font-medium text-academy hover:text-accent"
              >
                S&apos;inscrire à la formation
                <ArrowUpRight className="size-4" />
              </Link>
            )}
          </div>

          {!launchMode && (
          <div className="grid gap-10 sm:grid-cols-3 md:col-span-7">
            {columns.map((col) => (
              <div key={col.title}>
                <h4 className="eyebrow">{col.title}</h4>
                <ul className="mt-5 space-y-3">
                  {col.links.map((link) => (
                    <li key={link.href}>
                      {"external" in link && link.external ? (
                        <a
                          href={link.href}
                          target="_blank"
                          rel="noreferrer"
                          className="inline-flex items-center gap-1.5 text-sm text-ink hover:text-accent"
                        >
                          {link.label}
                          <ArrowUpRight className="size-3.5 opacity-60" />
                        </a>
                      ) : (
                        <Link
                          href={link.href}
                          className="text-sm text-ink hover:text-accent"
                        >
                          {link.label}
                        </Link>
                      )}
                    </li>
                  ))}
                </ul>
              </div>
            ))}
          </div>
          )}
        </div>

        <div className="mt-16 flex flex-col items-start justify-between gap-3 border-t border-line pt-6 text-xs text-muted md:flex-row md:items-center">
          <p>
            © {new Date().getFullYear()} {settings.site_title}. Tous droits réservés.
          </p>
          <p>{settings.address ?? site.location}</p>
        </div>
      </Container>
    </footer>
  );
}
