"use client";

import { useState } from "react";

interface SocialShareButtonsProps {
  url: string;
  title: string;
  text?: string;
  className?: string;
}

/**
 * Boutons de partage sur les réseaux sociaux.
 */
export function SocialShareButtons({
  url,
  title,
  text,
  className = "",
}: SocialShareButtonsProps) {
  const [copied, setCopied] = useState(false);
  const shareText = text ?? title;
  const encodedUrl = encodeURIComponent(url);
  const encodedTitle = encodeURIComponent(title);
  const encodedText = encodeURIComponent(shareText);

  const copyLink = async () => {
    try {
      await navigator.clipboard.writeText(url);
      setCopied(true);
      window.setTimeout(() => setCopied(false), 2000);
    } catch {
      window.prompt("Copiez ce lien :", url);
    }
  };

  const links = [
    {
      label: "WhatsApp",
      href: `https://wa.me/?text=${encodedText}%20${encodedUrl}`,
      className: "share-btn-whatsapp",
    },
    {
      label: "Facebook",
      href: `https://www.facebook.com/sharer/sharer.php?u=${encodedUrl}`,
      className: "share-btn-facebook",
    },
    {
      label: "X",
      href: `https://twitter.com/intent/tweet?url=${encodedUrl}&text=${encodedText}`,
      className: "share-btn-x",
    },
    {
      label: "LinkedIn",
      href: `https://www.linkedin.com/sharing/share-offsite/?url=${encodedUrl}`,
      className: "share-btn-linkedin",
    },
  ];

  return (
    <div className={`flex flex-wrap items-center gap-2 ${className}`}>
      <span className="mr-1 text-xs font-semibold uppercase tracking-wide text-muted">
        Partager
      </span>
      {links.map((link) => (
        <a
          key={link.label}
          href={link.href}
          target="_blank"
          rel="noopener noreferrer"
          className={`share-btn ${link.className}`}
        >
          {link.label}
        </a>
      ))}
      <button type="button" className="share-btn share-btn-copy" onClick={copyLink}>
        {copied ? "Copié !" : "Copier le lien"}
      </button>
    </div>
  );
}
