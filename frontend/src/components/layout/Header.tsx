"use client";

import Image from "next/image";
import Link from "next/link";
import { usePathname } from "next/navigation";
import { useEffect, useState } from "react";
import { ThemeToggle } from "@/components/layout/ThemeToggle";
import { siteLogoUrl, useSiteSettings } from "@/components/providers/SiteSettingsProvider";
import { NAV_LINKS } from "@/data/site";

/**
 * En-tête fixe avec navigation et logo dynamique.
 */
export function Header() {
  const settings = useSiteSettings();
  const logoSrc = siteLogoUrl(settings);
  const isRemoteLogo = logoSrc.startsWith("http");
  const pathname = usePathname();
  const isAcademyDetail = pathname.startsWith("/academy/");
  const [scrolled, setScrolled] = useState(false);
  const [menuOpen, setMenuOpen] = useState(false);

  useEffect(() => {
    const onScroll = () => {
      setScrolled(window.scrollY > 24);
    };

    onScroll();
    window.addEventListener("scroll", onScroll);
    return () => window.removeEventListener("scroll", onScroll);
  }, []);

  const logoNode = isRemoteLogo ? (
    // eslint-disable-next-line @next/next/no-img-element
    <img src={logoSrc} alt={settings.site_title} className="h-10 w-auto object-contain md:h-12" />
  ) : (
    <Image
      src={logoSrc}
      alt={settings.site_title}
      width={140}
      height={48}
      className="h-10 w-auto object-contain md:h-12"
      priority
    />
  );

  if (isAcademyDetail) {
    return (
      <header
        className={`site-header fixed inset-x-0 top-0 z-50 border-b py-4 transition-all duration-300 ${
          scrolled ? "is-scrolled" : ""
        }`}
      >
        <nav className="container flex items-center justify-between gap-4">
          <Link href="/" className="flex shrink-0 items-center gap-3">
            {logoNode}
          </Link>
          <div className="flex items-center gap-3">
            <ThemeToggle />
            <Link href="/" className="btn btn-outline">
              ← Retour à l&apos;accueil
            </Link>
          </div>
        </nav>
      </header>
    );
  }

  return (
    <header
      className={`site-header fixed inset-x-0 top-0 z-50 border-b py-5 transition-all duration-300 ${
        scrolled ? "is-scrolled py-3" : ""
      }`}
    >
      <nav className="container flex items-center gap-6">
        <Link href="/" className="flex shrink-0 items-center gap-3">
          {logoNode}
        </Link>

        <ul
          className={`${
            menuOpen
              ? "site-nav-mobile flex flex-col absolute left-0 right-0 top-[var(--header-h)] border-b p-6"
              : "hidden"
          } lg:static lg:ml-auto lg:flex lg:flex-row lg:border-0 lg:bg-transparent lg:p-0 gap-6`}
        >
          {NAV_LINKS.map((link) => (
            <li key={link.href}>
              <Link href={`/${link.href}`} className="site-nav-link" onClick={() => setMenuOpen(false)}>
                {link.label}
              </Link>
            </li>
          ))}
        </ul>

        <div className="ml-auto flex items-center gap-3 lg:ml-0">
          <ThemeToggle />
          <Link href="/#academy" className="btn btn-gold hidden sm:inline-flex">
            SDev Academy
          </Link>
          <button
            type="button"
            className="flex flex-col gap-1.5 p-2 lg:hidden"
            aria-label="Menu"
            onClick={() => setMenuOpen((open) => !open)}
          >
            <span className="block h-0.5 w-6 bg-[var(--color-text)]" />
            <span className="block h-0.5 w-6 bg-[var(--color-text)]" />
            <span className="block h-0.5 w-6 bg-[var(--color-text)]" />
          </button>
        </div>
      </nav>
    </header>
  );
}
