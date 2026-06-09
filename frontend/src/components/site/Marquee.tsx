import { cn } from "@/lib/cn";
import type { ReactNode } from "react";

export function Marquee({
  children,
  className,
}: {
  children: ReactNode;
  className?: string;
}) {
  return (
    <div className={cn("marquee overflow-hidden", className)}>
      <div className="marquee-track gap-12 py-4">
        <div className="flex shrink-0 items-center gap-12">{children}</div>
        <div aria-hidden className="flex shrink-0 items-center gap-12">
          {children}
        </div>
      </div>
    </div>
  );
}
