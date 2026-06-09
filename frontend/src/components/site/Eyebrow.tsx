import { cn } from "@/lib/cn";
import type { ReactNode } from "react";

export function Eyebrow({
  children,
  className,
  dot = true,
}: {
  children: ReactNode;
  className?: string;
  dot?: boolean;
}) {
  return (
    <span
      className={cn(
        "eyebrow inline-flex items-center gap-2",
        className,
      )}
    >
      {dot && (
        <span className="inline-block size-1.5 rounded-full bg-accent" />
      )}
      {children}
    </span>
  );
}
