import { NextResponse } from "next/server";
import type { NextRequest } from "next/server";
import {
  isAcademyLaunchMode,
  LAUNCH_HIDDEN_PATHS,
  PRIMARY_SESSION_SLUG,
} from "@/lib/launch";

/**
 * Redirige l'accueil et les pages en construction vers la session Academy active.
 */
export function middleware(request: NextRequest) {
  if (!isAcademyLaunchMode()) {
    return NextResponse.next();
  }

  const { pathname } = request.nextUrl;
  const sessionUrl = `/academy/${PRIMARY_SESSION_SLUG}`;

  if (pathname === "/") {
    return NextResponse.redirect(new URL(sessionUrl, request.url));
  }

  if (pathname === "/academy") {
    return NextResponse.redirect(new URL(sessionUrl, request.url));
  }

  const isHidden = LAUNCH_HIDDEN_PATHS.some(
    (hiddenPath) =>
      pathname === hiddenPath || pathname.startsWith(`${hiddenPath}/`),
  );

  if (isHidden) {
    return NextResponse.redirect(new URL(sessionUrl, request.url));
  }

  return NextResponse.next();
}

export const config = {
  matcher: [
    "/",
    "/academy",
    "/silas/:path*",
    "/studio/:path*",
    "/portfolio/:path*",
    "/contact",
  ],
};
