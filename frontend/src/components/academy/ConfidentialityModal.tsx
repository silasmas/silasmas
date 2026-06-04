"use client";

import { useEffect } from "react";

interface ConfidentialityModalProps {
  open: boolean;
  title: string;
  notice: string;
  resourceTitle?: string;
  onAccept: () => void;
  onClose: () => void;
}

/**
 * Modale de lecture de la notice de confidentialité avant accès à une ressource.
 */
export function ConfidentialityModal({
  open,
  title,
  notice,
  resourceTitle,
  onAccept,
  onClose,
}: ConfidentialityModalProps) {
  useEffect(() => {
    if (!open) {
      return;
    }

    const previous = document.body.style.overflow;
    document.body.style.overflow = "hidden";

    return () => {
      document.body.style.overflow = previous;
    };
  }, [open]);

  if (!open) {
    return null;
  }

  return (
    <div
      className="fixed inset-0 z-50 flex items-center justify-center bg-black/70 p-4"
      role="dialog"
      aria-modal="true"
      aria-labelledby="confidentiality-title"
    >
      <div className="glass max-h-[85vh] w-full max-w-lg overflow-hidden rounded-3xl shadow-2xl">
        <div className="border-b border-white/10 px-6 py-4">
          <h3 id="confidentiality-title" className="text-lg font-bold text-white">
            {title}
          </h3>
          {resourceTitle && (
            <p className="mt-1 text-sm text-amber-300/90">Ressource : {resourceTitle}</p>
          )}
        </div>
        <div className="max-h-[50vh] overflow-y-auto px-6 py-4 text-sm leading-relaxed text-slate-300 whitespace-pre-wrap">
          {notice}
        </div>
        <div className="flex flex-wrap justify-end gap-3 border-t border-white/10 px-6 py-4">
          <button type="button" className="btn btn-outline" onClick={onClose}>
            Annuler
          </button>
          <button type="button" className="btn btn-gold" onClick={onAccept}>
            J&apos;ai lu et j&apos;accepte — ouvrir la ressource
          </button>
        </div>
      </div>
    </div>
  );
}
