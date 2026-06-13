/**
 * Décode les entités HTML (contenu échappé par l'API ou double-encodé).
 */
export function decodeHtmlEntities(value: string): string {
  let decoded = value;

  for (let pass = 0; pass < 3; pass += 1) {
    const next = decoded
      .replace(/&nbsp;/gi, "\u00A0")
      .replace(/&lt;/gi, "<")
      .replace(/&gt;/gi, ">")
      .replace(/&quot;/gi, '"')
      .replace(/&#0?39;/gi, "'")
      .replace(/&#x27;/gi, "'")
      .replace(/&amp;/gi, "&");

    if (next === decoded) {
      break;
    }

    decoded = next;
  }

  return decoded;
}

/**
 * Indique si le contenu ressemble à du HTML (éditeur riche Filament).
 */
export function looksLikeHtml(value: string): boolean {
  return /<\/?[a-z][\s\S]*>/i.test(value);
}

/**
 * Supprime les balises HTML pour extraits SEO ou aperçus texte.
 */
export function stripHtml(value: string): string {
  return value
    .replace(/<br\s*\/?>/gi, " ")
    .replace(/<\/p>/gi, " ")
    .replace(/<[^>]+>/g, "")
    .replace(/\s+/g, " ")
    .trim();
}

/**
 * Nettoie le HTML admin avant affichage (contenu de confiance, sans scripts).
 */
export function sanitizeRichHtml(value: string): string {
  const trimmed = decodeHtmlEntities(value.trim());

  if (trimmed === "") {
    return "";
  }

  if (!looksLikeHtml(trimmed)) {
    return trimmed
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;")
      .replace(/\n/g, "<br>");
  }

  return trimmed
    .replace(/<script[\s\S]*?>[\s\S]*?<\/script>/gi, "")
    .replace(/<iframe[\s\S]*?>[\s\S]*?<\/iframe>/gi, "")
    .replace(/<style[\s\S]*?>[\s\S]*?<\/style>/gi, "")
    .replace(/\son\w+\s*=\s*("[^"]*"|'[^']*'|[^\s>]+)/gi, "")
    .replace(/javascript:/gi, "");
}

/**
 * Extrait un extrait texte court (meta description, modale…).
 */
export function richTextExcerpt(value: string | null | undefined, maxLength = 160): string {
  if (!value) {
    return "";
  }

  const plain = stripHtml(value);

  if (plain.length <= maxLength) {
    return plain;
  }

  return `${plain.slice(0, maxLength - 1).trim()}…`;
}
