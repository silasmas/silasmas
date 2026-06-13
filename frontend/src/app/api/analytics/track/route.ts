import { NextRequest, NextResponse } from "next/server";

const API_BASE =
  process.env.NEXT_PUBLIC_API_URL ?? "http://localhost:8000/api";

/** En-têtes géo / IP à transmettre à l'API Laravel. */
const FORWARDED_HEADERS = [
  "cf-ipcountry",
  "x-country-code",
  "cloudfront-viewer-country",
  "x-forwarded-for",
  "x-real-ip",
] as const;

/**
 * Proxy same-origin : le navigateur appelle silasmas.com, Next.js relaie vers api.silasmas.com.
 * Évite les blocages CORS entre le site vitrine et l'API.
 */
export async function POST(request: NextRequest) {
  let body: string;

  try {
    body = await request.text();
  } catch {
    return NextResponse.json(
      { success: false, message: "Corps de requête invalide." },
      { status: 400 }
    );
  }

  const upstreamHeaders: Record<string, string> = {
    Accept: "application/json",
    "Content-Type": "application/json",
  };

  for (const headerName of FORWARDED_HEADERS) {
    const value = request.headers.get(headerName);

    if (value) {
      upstreamHeaders[headerName] = value;
    }
  }

  try {
    const upstream = await fetch(`${API_BASE}/analytics/track`, {
      method: "POST",
      headers: upstreamHeaders,
      body,
      cache: "no-store",
    });

    const responseBody = await upstream.text();

    return new NextResponse(responseBody, {
      status: upstream.status,
      headers: {
        "Content-Type": upstream.headers.get("content-type") ?? "application/json",
      },
    });
  } catch {
    return NextResponse.json(
      { success: false, message: "Impossible de joindre l'API analytics." },
      { status: 502 }
    );
  }
}
