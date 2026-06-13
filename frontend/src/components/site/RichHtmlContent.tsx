"use client";

import { sanitizeRichHtml } from "@/lib/rich-html";

interface RichHtmlContentProps {
  html: string;
  className?: string;
}

/**
 * Affiche du contenu riche (HTML Filament) avec styles typographiques du site.
 */
export function RichHtmlContent({ html, className = "" }: RichHtmlContentProps) {
  const sanitized = sanitizeRichHtml(html);

  if (sanitized === "") {
    return null;
  }

  return (
    <div
      className={`rich-html ${className}`.trim()}
      dangerouslySetInnerHTML={{ __html: sanitized }}
    />
  );
}
