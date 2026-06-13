"use client";

import { useLayoutEffect, useRef, useState } from "react";
import { RichHtmlContent } from "@/components/site/RichHtmlContent";

interface RichHtmlReadMoreProps {
  html: string;
  className?: string;
  /** Hauteur max du bloc replié. */
  collapsedMaxHeight?: string;
  /** Preset visuel : modale (compact) ou carte session (plus haut). */
  variant?: "modal" | "card";
}

const VARIANT_MAX_HEIGHT = {
  modal: "6.5rem",
  card: "11rem",
} as const;

/**
 * Affiche du HTML riche repliable avec « Lire plus / Lire moins » si le contenu dépasse la hauteur limite.
 *
 * @param html Contenu HTML (Filament)
 * @param className Classes sur le conteneur
 * @param collapsedMaxHeight Hauteur CSS max en mode replié
 * @param variant Preset modal ou carte
 */
export function RichHtmlReadMore({
  html,
  className = "",
  collapsedMaxHeight,
  variant = "modal",
}: RichHtmlReadMoreProps) {
  const [expanded, setExpanded] = useState(false);
  const [collapsible, setCollapsible] = useState(false);
  const wrapperRef = useRef<HTMLDivElement>(null);
  const maxHeight = collapsedMaxHeight ?? VARIANT_MAX_HEIGHT[variant];

  useLayoutEffect(() => {
    const element = wrapperRef.current;

    if (!element) {
      return;
    }

    element.style.maxHeight = maxHeight;
    element.style.overflow = "hidden";
    const overflows = element.scrollHeight > element.clientHeight + 4;
    element.style.maxHeight = "";
    element.style.overflow = "";

    setCollapsible(overflows);
  }, [html, maxHeight]);

  const isCollapsed = collapsible && !expanded;

  return (
    <div className={className}>
      <div
        ref={wrapperRef}
        className={
          isCollapsed
            ? variant === "card"
              ? "rich-html-read-more--collapsed rich-html-read-more--card"
              : "rich-html-read-more--collapsed"
            : undefined
        }
        style={isCollapsed ? { maxHeight } : undefined}
      >
        <RichHtmlContent html={html} />
      </div>
      {collapsible && (
        <button
          type="button"
          className="mt-3 inline-flex items-center gap-1 text-sm font-semibold text-academy transition hover:underline"
          onClick={() => {
            setExpanded((value) => !value);
          }}
          aria-expanded={expanded}
        >
          {expanded ? "Lire moins" : "Lire plus"}
          <span aria-hidden className="text-base leading-none">
            {expanded ? "↑" : "↓"}
          </span>
        </button>
      )}
    </div>
  );
}
