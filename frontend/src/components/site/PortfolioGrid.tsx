"use client";

import Link from "next/link";
import Image from "next/image";
import { useMemo, useState } from "react";
import { ArrowUpRight } from "lucide-react";
import { motion, AnimatePresence } from "framer-motion";
import { cn } from "@/lib/cn";
import type { Project } from "@/lib/content";

const categories = ["Tous", "Site Web", "Application", "Plateforme", "Branding"] as const;
type Category = (typeof categories)[number];

export function PortfolioGrid({ projects }: { projects: Project[] }) {
  const [active, setActive] = useState<Category>("Tous");

  const filtered = useMemo(() => {
    if (active === "Tous") return projects;
    return projects.filter((p) => p.category === active);
  }, [active, projects]);

  return (
    <div>
      <div className="flex flex-wrap items-center gap-2">
        {categories.map((c) => (
          <button
            key={c}
            type="button"
            onClick={() => setActive(c)}
            className={cn(
              "h-9 rounded-full border px-4 text-sm transition-colors",
              active === c
                ? "surface-strong border-transparent"
                : "border-line bg-bg-elev text-ink-soft hover:border-line-strong",
            )}
          >
            {c}
          </button>
        ))}
        <span className="ml-auto text-sm text-muted">
          {filtered.length} projet{filtered.length > 1 ? "s" : ""}
        </span>
      </div>

      <div className="mt-10 grid gap-5 md:grid-cols-12 md:gap-6">
        <AnimatePresence mode="popLayout">
          {filtered.map((p, i) => {
            const span =
              i % 5 === 0
                ? "md:col-span-7"
                : i % 5 === 1
                  ? "md:col-span-5"
                  : i % 5 === 2
                    ? "md:col-span-4"
                    : i % 5 === 3
                      ? "md:col-span-4"
                      : "md:col-span-4";
            return (
              <motion.div
                key={p.slug}
                layout
                initial={{ opacity: 0, y: 10 }}
                animate={{ opacity: 1, y: 0 }}
                exit={{ opacity: 0, y: 10 }}
                transition={{ duration: 0.3 }}
                className={span}
              >
                <Card project={p} />
              </motion.div>
            );
          })}
        </AnimatePresence>
      </div>
    </div>
  );
}

function Card({ project }: { project: Project }) {
  return (
    <Link
      href={`/portfolio/${project.slug}`}
      className="group block h-full overflow-hidden rounded-[24px] border border-line bg-bg-elev transition-colors hover:border-line-strong"
    >
      <div className="relative aspect-[4/3] overflow-hidden">
        <Image
          src={project.cover}
          alt={project.title}
          fill
          sizes="(min-width: 768px) 33vw, 100vw"
          className="object-cover transition-transform duration-700 ease-out group-hover:scale-[1.04]"
        />
        <div
          aria-hidden
          className="absolute inset-0 bg-gradient-to-t from-ink/55 via-ink/10 to-transparent"
        />
        <span className="surface-strong absolute right-5 top-5 inline-flex size-10 items-center justify-center rounded-full transition-transform duration-300 group-hover:-translate-y-0.5 group-hover:translate-x-0.5">
          <ArrowUpRight className="size-4" />
        </span>
        <div className="absolute bottom-5 left-5">
          <span className="inline-flex items-center rounded-full border border-white/30 bg-white/15 px-3 py-1 text-xs text-white backdrop-blur-md">
            {project.category} · {project.year}
          </span>
        </div>
      </div>
      <div className="border-t border-line p-6">
        <h3 className="font-display text-2xl tracking-tight md:text-3xl">
          {project.title}
        </h3>
        <p className="mt-2 text-sm text-muted">{project.excerpt}</p>
      </div>
    </Link>
  );
}
