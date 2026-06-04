import type { ReactNode } from "react";

interface SkeletonProps {
  className?: string;
}

/**
 * Bloc skeleton animé (rectangle).
 */
export function SkeletonBlock({ className = "" }: SkeletonProps) {
  return <div className={`skeleton-block ${className}`.trim()} aria-hidden />;
}

/**
 * Ligne skeleton animée (texte).
 */
export function SkeletonLine({ className = "" }: SkeletonProps) {
  return <div className={`skeleton-line ${className}`.trim()} aria-hidden />;
}

interface SkeletonSectionProps {
  children: ReactNode;
  label?: string;
}

/**
 * Enveloppe de section en chargement (accessibilité).
 */
export function SkeletonSection({ children, label = "Chargement du contenu" }: SkeletonSectionProps) {
  return (
    <div className="page-skeleton" role="status" aria-live="polite" aria-label={label}>
      {children}
      <span className="sr-only">{label}</span>
    </div>
  );
}

/**
 * En-tête de section skeleton (eyebrow + titre).
 */
export function SkeletonSectionHeader({ centered = false }: { centered?: boolean }) {
  return (
    <div className={`mb-12 ${centered ? "text-center" : ""}`}>
      <SkeletonLine className={`mb-4 h-6 w-28 ${centered ? "mx-auto" : ""}`} />
      <SkeletonLine className={`h-10 w-64 max-w-full ${centered ? "mx-auto" : ""}`} />
      <SkeletonLine className={`mt-4 h-4 w-96 max-w-full ${centered ? "mx-auto" : ""}`} />
    </div>
  );
}
