"use client";

import { useState } from "react";
import { RichHtmlContent } from "@/components/site/RichHtmlContent";
import { stripHtml } from "@/lib/rich-html";

interface RichHtmlReadMoreProps {
  html: string;
  className?: string;
  /** Seuil en caractères (texte brut) pour afficher le bouton. */
  maxChars?: number;
  /** Hauteur max du bloc replié. */
  collapsedMaxHeight?: string;
}

/**
 * Affiche du HTML riche avec repli « Lire plus / Lire moins » si le contenu est long.
 *
 * @param html Contenu HTML (Filament)
 * @param className Classes sur le conteneur
 * @param maxChars Seuil de longueur avant repli
 * @param collapsedMaxHeight Hauteur CSS max en mode replié
 */
export function RichHtmlReadMore({
  html,
  className = "",
  maxChars = 220,
  collapsedMaxHeight = "6.5rem",
}: RichHtmlReadMoreProps) {
  const [expanded, setExpanded] = useState(false);
  const isLong = stripHtml(html).length > maxChars;

  if (!isLong) {
    return <RichHtmlContent html={html} className={className} />;
  }

  return (
    <div className={className}>
      <div
        className={expanded ? undefined : "rich-html-read-more--collapsed"}
        style={expanded ? undefined : { maxHeight: collapsedMaxHeight }}
      >
        <RichHtmlContent html={html} />
      </div>
      <button
        type="button"
        className="mt-2 text-sm font-semibold text-academy transition hover:underline"
        onClick={() => {
          setExpanded((value) => !value);
        }}
        aria-expanded={expanded}
      >
        {expanded ? "Lire moins" : "Lire plus"}
      </button>
    </div>
  );
}
