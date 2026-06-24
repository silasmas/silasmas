import { NextRequest, NextResponse } from "next/server";

const API_BASE =
  process.env.NEXT_PUBLIC_API_URL ?? "http://localhost:8000/api";

interface RouteContext {
  params: Promise<{
    token: string;
  }>;
}

/**
 * Proxy same-origin pour la reprise d'inscription (évite les blocages CORS).
 */
export async function GET(_request: NextRequest, context: RouteContext) {
  const { token } = await context.params;

  try {
    const upstream = await fetch(
      `${API_BASE}/academy/registrations/resume/${encodeURIComponent(token)}`,
      {
        headers: { Accept: "application/json" },
        cache: "no-store",
      }
    );

    const responseBody = await upstream.text();

    return new NextResponse(responseBody, {
      status: upstream.status,
      headers: {
        "Content-Type": upstream.headers.get("content-type") ?? "application/json",
      },
    });
  } catch {
    return NextResponse.json(
      { success: false, message: "Impossible de joindre l'API Academy." },
      { status: 502 }
    );
  }
}
