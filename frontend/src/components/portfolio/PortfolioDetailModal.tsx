"use client";

import { useEffect, useState } from "react";
import { projectLogoUrl } from "@/lib/projects";
import type { Project } from "@/types/api";

interface PortfolioDetailModalProps {
  project: Project | null;
  onClose: () => void;
}

/**
 * Modale plein écran de détail d'un projet portfolio.
 */
export function PortfolioDetailModal({ project, onClose }: PortfolioDetailModalProps) {
  const [slideIndex, setSlideIndex] = useState(0);

  useEffect(() => {
    if (!project) {
      return undefined;
    }

    setSlideIndex(0);
    const onKeyDown = (event: KeyboardEvent) => {
      if (event.key === "Escape") {
        onClose();
      }
    };

    document.body.style.overflow = "hidden";
    window.addEventListener("keydown", onKeyDown);

    return () => {
      document.body.style.overflow = "";
      window.removeEventListener("keydown", onKeyDown);
    };
  }, [project, onClose]);

  if (!project) {
    return null;
  }

  const gallery =
    project.gallery_urls && project.gallery_urls.length > 0
      ? project.gallery_urls
      : [projectLogoUrl(project)];
  const logoUrl = projectLogoUrl(project);
  const currentImage = gallery[slideIndex] ?? logoUrl;

  return (
    <div className="portfolio-modal-overlay" role="dialog" aria-modal="true">
      <div className="portfolio-modal">
        <button type="button" className="portfolio-modal-close" onClick={onClose} aria-label="Fermer">
          ×
        </button>

        <div className="portfolio-modal-grid">
          <div className="portfolio-modal-gallery">
            <div className="portfolio-modal-image-wrap">
              {/* eslint-disable-next-line @next/next/no-img-element */}
              <img src={currentImage} alt={project.project_name} className="portfolio-modal-image" />
            </div>
            {gallery.length > 1 && (
              <div className="portfolio-modal-thumbs">
                {gallery.map((url, index) => (
                  <button
                    key={`${project.id}-thumb-${index}`}
                    type="button"
                    className={`portfolio-modal-thumb ${index === slideIndex ? "is-active" : ""}`}
                    onClick={() => setSlideIndex(index)}
                  >
                    {/* eslint-disable-next-line @next/next/no-img-element */}
                    <img src={url} alt="" />
                  </button>
                ))}
              </div>
            )}
          </div>

          <div className="portfolio-modal-content">
            <div className="portfolio-modal-brand">
              {/* eslint-disable-next-line @next/next/no-img-element */}
              <img src={logoUrl} alt={project.project_name} className="portfolio-modal-logo" />
              <div>
                <p className="section-eyebrow mb-2">{project.category ?? "Projet"}</p>
                <h2 className="text-2xl font-bold">{project.project_name}</h2>
                {project.client_name && (
                  <p className="mt-1 text-sm text-muted">Client : {project.client_name}</p>
                )}
              </div>
            </div>

            {project.project_date && (
              <p className="text-sm text-accent">Date : {project.project_date}</p>
            )}

            {project.project_description && (
              <p className="text-muted">{project.project_description}</p>
            )}

            <ul className="portfolio-modal-links space-y-2 text-sm">
              {project.web_url && (
                <li>
                  <a href={project.web_url} target="_blank" rel="noopener noreferrer" className="text-accent hover:underline">
                    Site web →
                  </a>
                </li>
              )}
              {project.android_url && (
                <li>
                  <a href={project.android_url} target="_blank" rel="noopener noreferrer" className="text-accent hover:underline">
                    Android →
                  </a>
                </li>
              )}
              {project.ios_url && (
                <li>
                  <a href={project.ios_url} target="_blank" rel="noopener noreferrer" className="text-accent hover:underline">
                    iOS →
                  </a>
                </li>
              )}
            </ul>
          </div>
        </div>
      </div>
    </div>
  );
}
