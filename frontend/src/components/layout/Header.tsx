"use client";

import Image from "next/image";
import Link from "next/link";
import { useEffect, useState } from "react";
import { NAV_LINKS } from "@/data/site";

/**
 * En-tête fixe avec navigation et logo SDev.
 */
export function Header() {
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

  return (
    <header
      className={`fixed inset-x-0 top-0 z-50 border-b transition-all duration-300 ${
        scrolled
          ? "border-amber-500/20 bg-black/80 py-3 shadow-lg backdrop-blur-xl"
          : "border-transparent bg-transparent py-5"
      }`}
    >
      <nav className="container flex items-center gap-6">
        <Link href="/" className="flex shrink-0 items-center gap-3">
          <Image
            src="/images/logo.png"
            alt="Silas Développe"
            width={140}
            height={48}
            className="h-10 w-auto object-contain md:h-12"
            priority
          />
        </Link>

        <ul
          className={`${
            menuOpen
              ? "flex flex-col absolute left-0 right-0 top-[var(--header-h)] border-b border-amber-500/20 bg-black/95 p-6"
              : "hidden"
          } lg:static lg:ml-auto lg:flex lg:flex-row lg:border-0 lg:bg-transparent lg:p-0 gap-6`}
        >
          {NAV_LINKS.map((link) => (
            <li key={link.href}>
              <a
                href={link.href}
                className="text-sm font-medium text-slate-400 transition hover:text-amber-400"
                onClick={() => setMenuOpen(false)}
              >
                {link.label}
              </a>
            </li>
          ))}
        </ul>

        <div className="ml-auto flex items-center gap-3 lg:ml-0">
          <a href="#academy" className="btn btn-gold hidden sm:inline-flex">
            SDev Academy
          </a>
          <button
            type="button"
            className="flex flex-col gap-1.5 p-2 lg:hidden"
            aria-label="Menu"
            onClick={() => setMenuOpen((open) => !open)}
          >
            <span className="block h-0.5 w-6 bg-white" />
            <span className="block h-0.5 w-6 bg-white" />
            <span className="block h-0.5 w-6 bg-white" />
          </button>
        </div>
      </nav>
    </header>
  );
}
