"use client";

import { useMemo, useState } from "react";
import { PortfolioDetailModal } from "@/components/portfolio/PortfolioDetailModal";
import { projectLogoUrl } from "@/lib/projects";
import type { Project } from "@/types/api";

type PortfolioFilter = "all" | "web" | "app";

interface PortfolioSectionProps {
  projects: Project[];
}

function getProjectCategories(project: Project): PortfolioFilter[] {
  const categories: PortfolioFilter[] = ["all"];
  const description = (project.project_description ?? "").toLowerCase();
  const category = (project.category ?? "").toLowerCase();

  if (project.web_url || description.includes("site") || category.includes("site")) {
    categories.push("web");
  }

  if (
    project.android_url ||
    project.ios_url ||
    description.includes("app") ||
    description.includes("mobile") ||
    category.includes("mobile")
  ) {
    categories.push("app");
  }

  if (categories.length === 1) {
    categories.push("web");
  }

  return categories;
}

/**
 * Section portfolio cliquable avec modale plein écran.
 */
export function PortfolioSection({ projects }: PortfolioSectionProps) {
  const [filter, setFilter] = useState<PortfolioFilter>("all");
  const [activeProject, setActiveProject] = useState<Project | null>(null);

  const filteredProjects = useMemo(() => {
    if (filter === "all") {
      return projects;
    }

    return projects.filter((project) =>
      getProjectCategories(project).includes(filter)
    );
  }, [filter, projects]);

  return (
    <>
      <section id="portfolio" className="section section-dark">
        <div className="container">
          <div className="mb-10 text-center">
            <span className="section-eyebrow">Réalisations</span>
            <h2 className="section-title">Portfolio</h2>
            <p className="section-subtitle mx-auto">
              Cliquez sur un projet pour voir les détails et la galerie.
            </p>
          </div>

          <div className="mb-10 flex flex-wrap justify-center gap-3">
            {[
              { id: "all", label: "Tous" },
              { id: "web", label: "Site Web" },
              { id: "app", label: "Application" },
            ].map((item) => (
              <button
                key={item.id}
                type="button"
                onClick={() => setFilter(item.id as PortfolioFilter)}
                className={`rounded-full px-5 py-2 text-sm font-medium transition ${
                  filter === item.id
                    ? "bg-amber-500 text-black"
                    : "border border-[var(--color-border)] text-muted hover:border-[var(--color-accent)]"
                }`}
              >
                {item.label}
              </button>
            ))}
          </div>

          <div className="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            {filteredProjects.map((project) => {
              const imageUrl = projectLogoUrl(project);

              return (
                <button
                  key={project.id}
                  type="button"
                  className="portfolio-card group text-left"
                  onClick={() => setActiveProject(project)}
                >
                  <div className="relative aspect-[4/3] overflow-hidden bg-[var(--color-bg-2)]">
                    {/* eslint-disable-next-line @next/next/no-img-element */}
                    <img
                      src={imageUrl}
                      alt={project.project_name}
                      className="h-full w-full object-cover transition duration-500 group-hover:scale-105"
                    />
                    <div className="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent" />
                    <div className="absolute bottom-0 p-5">
                      <h3 className="text-xl font-semibold text-white">{project.project_name}</h3>
                      <p className="line-clamp-2 text-sm text-slate-200">
                        {project.project_description}
                      </p>
                      <span className="mt-2 inline-block text-xs font-semibold text-amber-300">
                        Voir le détail →
                      </span>
                    </div>
                  </div>
                </button>
              );
            })}
          </div>
        </div>
      </section>

      <PortfolioDetailModal project={activeProject} onClose={() => setActiveProject(null)} />
    </>
  );
}
