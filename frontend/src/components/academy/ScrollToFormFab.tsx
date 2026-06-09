"use client";

import { ArrowDown } from "lucide-react";
import { useEffect, useState } from "react";

/**
 * Bouton mobile fixe qui fait défiler vers le formulaire d'inscription.
 */
export function ScrollToFormFab() {
  const [visible, setVisible] = useState(false);

  useEffect(() => {
    const target = document.getElementById("inscription");

    if (!target) {
      return;
    }

    const observer = new IntersectionObserver(
      ([entry]) => {
        setVisible(!entry.isIntersecting);
      },
      { threshold: 0.12, rootMargin: "-72px 0px -40% 0px" },
    );

    observer.observe(target);

    return () => {
      observer.disconnect();
    };
  }, []);

  const scrollToForm = () => {
    document.getElementById("inscription")?.scrollIntoView({
      behavior: "smooth",
      block: "start",
    });
  };

  if (!visible) {
    return null;
  }

  return (
    <button
      type="button"
      onClick={scrollToForm}
      className="fixed top-20 right-4 z-40 inline-flex max-w-[calc(100vw-2rem)] items-center gap-1.5 rounded-full border border-line bg-bg-elev/95 px-3.5 py-2 text-xs font-semibold text-ink shadow-[0_8px_24px_rgba(0,0,0,0.12)] backdrop-blur-md md:hidden"
      aria-label="Aller au formulaire d'inscription"
    >
      <span className="truncate">Aller au formulaire</span>
      <ArrowDown className="size-3.5 shrink-0 text-academy" aria-hidden />
    </button>
  );
}
