import { cn } from "@/lib/cn";
import { Eyebrow } from "./Eyebrow";
import type { ReactNode } from "react";

export function SectionHeader({
  eyebrow,
  title,
  intro,
  align = "left",
  className,
}: {
  eyebrow?: string;
  title: ReactNode;
  intro?: ReactNode;
  align?: "left" | "center";
  className?: string;
}) {
  return (
    <div
      className={cn(
        "max-w-3xl",
        align === "center" && "mx-auto text-center",
        className,
      )}
    >
      {eyebrow && <Eyebrow>{eyebrow}</Eyebrow>}
      <h2
        className={cn(
          "font-display mt-4 text-4xl leading-[1.05] tracking-tight md:text-5xl lg:text-6xl",
        )}
      >
        {title}
      </h2>
      {intro && (
        <p className="mt-5 text-lg text-muted leading-relaxed md:text-xl">
          {intro}
        </p>
      )}
    </div>
  );
}
