import { NextResponse } from "next/server";
import type { NextRequest } from "next/server";
import {
  isAcademyLaunchMode,
  LAUNCH_HIDDEN_PATHS,
  resolveLaunchRedirectPath,
} from "@/lib/launch";

/**
 * Redirige l'accueil et les pages masquées vers la session Academy active.
 * Évite de servir /academy (page hub) dont le cache CDN peut confondre HTML et RSC.
 */
export async function middleware(request: NextRequest) {
  if (!isAcademyLaunchMode()) {
    return NextResponse.next();
  }

  const { pathname } = request.nextUrl;
  const isAcademyHub =
    pathname === "/academy" || pathname === "/academy/";
  const isHome = pathname === "/";
  const isHidden = LAUNCH_HIDDEN_PATHS.some(
    (hiddenPath) =>
      pathname === hiddenPath || pathname.startsWith(`${hiddenPath}/`),
  );

  if (!isHome && !isAcademyHub && !isHidden) {
    return NextResponse.next();
  }

  const target = await resolveLaunchRedirectPath();
  const response = NextResponse.redirect(new URL(target, request.url));

  response.headers.set("Cache-Control", "no-store, no-cache, must-revalidate");

  return response;
}

export const config = {
  matcher: [
    "/",
    "/academy",
    "/academy/",
    "/silas/:path*",
    "/studio/:path*",
    "/portfolio/:path*",
    "/contact",
  ],
};
