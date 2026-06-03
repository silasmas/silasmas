"use client";

import Image from "next/image";
import { useMemo, useState } from "react";
import { resolveStorageUrl } from "@/lib/api";
import type { Project } from "@/types/api";

type PortfolioFilter = "all" | "web" | "app";

interface PortfolioSectionProps {
  projects: Project[];
}

/**
 * Détermine les catégories d'un projet pour le filtre portfolio.
 */
function getProjectCategories(project: Project): PortfolioFilter[] {
  const categories: PortfolioFilter[] = ["all"];
  const description = (project.project_description ?? "").toLowerCase();

  if (project.web_url || description.includes("site")) {
    categories.push("web");
  }

  if (
    project.android_url ||
    project.ios_url ||
    description.includes("app") ||
    description.includes("mobile")
  ) {
    categories.push("app");
  }

  if (categories.length === 1) {
    categories.push("web");
  }

  return categories;
}

/**
 * Section portfolio avec filtres et données API.
 */
export function PortfolioSection({ projects }: PortfolioSectionProps) {
  const [filter, setFilter] = useState<PortfolioFilter>("all");

  const filteredProjects = useMemo(() => {
    if (filter === "all") {
      return projects;
    }

    return projects.filter((project) =>
      getProjectCategories(project).includes(filter)
    );
  }, [filter, projects]);

  return (
    <section id="portfolio" className="section section-dark">
      <div className="container">
        <div className="mb-10 text-center">
          <span className="section-eyebrow">Réalisations</span>
          <h2 className="section-title">Portfolio</h2>
          <p className="section-subtitle mx-auto">
            Dans cette expérience nous avons réalisé de nombreux projets, présentés
            ci-dessous pour ceux autorisés par nos clients.
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
                  : "border border-white/10 text-slate-300 hover:border-amber-500/40"
              }`}
            >
              {item.label}
            </button>
          ))}
        </div>

        <div className="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
          {filteredProjects.map((project) => {
            const imageUrl =
              resolveStorageUrl(project.logo_url) ?? project.logo_url ?? "/images/logo.png";

            return (
              <article
                key={project.id}
                className="group overflow-hidden rounded-3xl border border-white/10 bg-black/40"
              >
                <div className="relative aspect-[4/3] overflow-hidden bg-[#111]">
                  <Image
                    src={imageUrl}
                    alt={project.project_name}
                    fill
                    className="object-cover transition duration-500 group-hover:scale-105"
                    sizes="(max-width: 768px) 100vw, 33vw"
                  />
                  <div className="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent" />
                  <div className="absolute bottom-0 p-5">
                    <h3 className="text-xl font-semibold">{project.project_name}</h3>
                    <p className="text-sm text-slate-300">
                      {project.project_description}
                    </p>
                  </div>
                </div>
              </article>
            );
          })}
        </div>
      </div>
    </section>
  );
}
